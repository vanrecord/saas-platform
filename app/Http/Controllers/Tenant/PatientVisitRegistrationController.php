<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Doctor;
use App\Models\Tenant\MedicalHistory;
use App\Models\Tenant\Patient;
use App\Models\Tenant\PatientVisit;
use App\Models\Tenant\Prefix;
use App\Models\Tenant\Reception;
use App\Models\UserTenant;
use App\Repositories\InvoicePrefixRepositoryInterface;
use App\Repositories\InvoiceRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PatientVisitRegistrationController extends Controller
{
    protected InvoiceRepositoryInterface $invoiceRepository;
    protected InvoicePrefixRepositoryInterface $prefixRepository;

    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository,
        InvoicePrefixRepositoryInterface $prefixRepository
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->prefixRepository = $prefixRepository;
    }

    /**
     * Display the Patient Visit Registration page.
     */
    public function create(Request $request): Response
    {
        abort_if(!auth()->user()?->can('register:view'), 403, 'Unauthorized to view register.');

        $selectedPatient = null;
        $existingInvoices = [];

        if ($request->filled('patient_id')) {
            $selectedPatient = Patient::find($request->query('patient_id'));
            if ($selectedPatient) {
                $existingInvoices = $this->formatPatientInvoices($selectedPatient);
            }
        }

        // Retrieve doctors from tenant staff/users
        $doctors = Doctor::with('user.profile')
            ->get()
            ->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'name' => $doc->full_name ?: ($doc->user?->name ?? 'Dr. #' . $doc->id),
                    'email' => $doc->email,
                ];
            });

        if ($doctors->isEmpty()) {
            $doctors = UserTenant::select('id', 'name', 'email')->get();
        }

        if ($doctors->isEmpty()) {
            $doctors = collect([
                ['id' => 1, 'name' => 'Dr. Sovann (Dental Surgeon)', 'email' => ''],
                ['id' => 2, 'name' => 'Dr. Chan (Orthodontist)', 'email' => ''],
                ['id' => 3, 'name' => 'Dr. Sophea (Pediatric Dentist)', 'email' => ''],
                ['id' => 4, 'name' => 'Dr. Vicheka (Periodontist)', 'email' => ''],
            ]);
        }

        return Inertia::render('Tenant/Visit/Register', [
            'selectedPatient' => $selectedPatient,
            'existingInvoices' => $existingInvoices,
            'doctors' => $doctors,
            'suggestedInvoiceReference' => PatientVisit::generateInvoiceReference(),
            'tenant' => [
                'name' => tenant('name') ?? 'Dental Clinic',
                'company' => tenant('company') ?? tenant('name') ?? 'Dental Clinic • OdontoCare',
                'logo' => tenant('logo') ?? null,
            ],
        ]);
    }

    /**
     * Store a newly created patient visit registration.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'workflow_option' => ['required', 'in:new_invoice,follow_up'],
            'invoice_id' => ['nullable', 'required_if:workflow_option,follow_up', 'exists:medical_histories,id'],
            'visit_type' => ['required', 'string', 'max:255'],
            'doctor_id' => ['nullable'],
            'doctor_name' => ['nullable', 'string', 'max:255'],
            'chief_complaint' => ['nullable', 'string', 'max:1000'],
            'clinical_notes' => ['nullable', 'string', 'max:2000'],
            'reception_type' => ['nullable', 'string', 'max:255'],
            'outstanding_balance' => ['nullable', 'numeric', 'min:0'],
        ]);

        $patient = Patient::findOrFail($validated['patient_id']);

        $invoice = DB::transaction(function () use ($validated, $patient) {
            $doctorId = !empty($validated['doctor_id']) ? (int) $validated['doctor_id'] : null;

            if ($validated['workflow_option'] === 'new_invoice') {
                // Option A: Create New Invoice
                $defaultPrefix = $this->prefixRepository->getDefault() ?: Prefix::first();
                $prefixId = $defaultPrefix?->id;
                $dailyDate = Carbon::today()->format('Y-m-d');
                $numberInfo = $this->invoiceRepository->generateNextInvoiceNumber($prefixId, $dailyDate);

                $rate = 4050.0;
                $setting = DB::table('settings')->where('setting_key', 'exchange_rate')->first();
                if ($setting && is_numeric($setting->setting_value)) {
                    $rate = (float) $setting->setting_value;
                }

                $invoice = MedicalHistory::create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctorId,
                    'user_id' => auth()->id(),
                    'invoice_id' => $numberInfo['invoice_id'],
                    'invoice_num_year_series' => $numberInfo['invoice_num_year_series'],
                    'prefix_invoice' => $numberInfo['prefix_invoice'],
                    'prefix_format' => $numberInfo['prefix_format'],
                    'prefix_id' => $numberInfo['prefix_id'],
                    'date' => Carbon::today(),
                    'visit_type' => $validated['visit_type'],
                    'diagnosis' => null,
                    'description' => $validated['chief_complaint'] ?? null,
                    'chief_complaint' => $validated['chief_complaint'] ?? null,
                    'rate' => $rate,
                    'discount_invoice' => 0.0,
                    'discount_inv_as' => '%',
                    'status' => MedicalHistory::UNPAID, // 2 (Unpaid)
                    'type' => 'invoice',
                ]);

                // Create Reception linked to the new Invoice
                Reception::create([
                    'reception_number' => Reception::generateReceptionNumber(),
                    'patient_id' => $patient->id,
                    'invoice_id' => $invoice->id,
                    'doctor_id' => $doctorId,
                    'reception_date' => Carbon::now(),
                    'visit_type' => $validated['visit_type'],
                    'chief_complaint' => $validated['chief_complaint'] ?? null,
                    'status' => 'registered',
                    'notes' => $validated['clinical_notes'] ?? null,
                    'created_by' => auth()->id(),
                ]);

                // Keep patient_visits synchronized for dashboard metrics
                PatientVisit::create([
                    'patient_id' => $patient->id,
                    'visit_number' => PatientVisit::generateVisitNumber(),
                    'workflow_option' => 'new_invoice',
                    'invoice_reference' => $invoice->invoice_number_formatted,
                    'visit_type' => $validated['visit_type'],
                    'doctor_id' => $doctorId,
                    'doctor_name' => $validated['doctor_name'] ?? 'Attending Dentist',
                    'chief_complaint' => $validated['chief_complaint'] ?? null,
                    'clinical_notes' => $validated['clinical_notes'] ?? null,
                    'reception_type' => $validated['reception_type'] ?? 'Initial Visit (Checked In)',
                    'outstanding_balance' => 0.00,
                    'status' => 'checked_in',
                    'visited_at' => Carbon::now(),
                ]);

                return $invoice;
            } else {
                // Option B: Select Existing Invoice (Follow-up Visit)
                $invoice = MedicalHistory::where('id', $validated['invoice_id'])
                    ->where('patient_id', $patient->id)
                    ->firstOrFail();

                // Create Reception linked to existing invoice (do NOT duplicate or alter invoice)
                Reception::create([
                    'reception_number' => Reception::generateReceptionNumber(),
                    'patient_id' => $patient->id,
                    'invoice_id' => $invoice->id,
                    'doctor_id' => $doctorId,
                    'reception_date' => Carbon::now(),
                    'visit_type' => $validated['visit_type'],
                    'chief_complaint' => $validated['chief_complaint'] ?? null,
                    'status' => 'registered',
                    'notes' => $validated['clinical_notes'] ?? null,
                    'created_by' => auth()->id(),
                ]);

                // Keep patient_visits synchronized for dashboard metrics
                PatientVisit::create([
                    'patient_id' => $patient->id,
                    'visit_number' => PatientVisit::generateVisitNumber(),
                    'workflow_option' => 'follow_up',
                    'invoice_reference' => $invoice->invoice_number_formatted,
                    'visit_type' => $validated['visit_type'],
                    'doctor_id' => $doctorId,
                    'doctor_name' => $validated['doctor_name'] ?? 'Attending Dentist',
                    'chief_complaint' => $validated['chief_complaint'] ?? null,
                    'clinical_notes' => $validated['clinical_notes'] ?? null,
                    'reception_type' => $validated['reception_type'] ?? 'Follow-up Consultation',
                    'outstanding_balance' => (float) ($invoice->balance ?? 0.00),
                    'status' => 'checked_in',
                    'visited_at' => Carbon::now(),
                ]);

                return $invoice;
            }
        });

        return redirect()->route('invoices.edit', $invoice->id)
            ->with('success', "Patient {$patient->full_name} has been successfully registered for visit. Redirected to Invoice #{$invoice->invoice_number_formatted}.");
    }

    /**
     * API: Get all existing invoices for a specific patient sorted by date DESC.
     */
    public function getPatientInvoices(Patient $patient): JsonResponse
    {
        return response()->json($this->formatPatientInvoices($patient));
    }

    /**
     * Format invoices list for patient.
     */
    protected function formatPatientInvoices(Patient $patient)
    {
        return MedicalHistory::where('patient_id', $patient->id)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get()
            ->map(function ($inv) {
                $invNum = $inv->invoice_number_formatted;
                $invDate = $inv->date ? Carbon::parse($inv->date)->format('Y-m-d') : 'N/A';
                $status = $inv->status_label;
                $balance = '$' . number_format((float) ($inv->balance ?? 0), 2);
                return [
                    'id' => $inv->id,
                    'invoice_number' => $invNum,
                    'date' => $invDate,
                    'status' => $status,
                    'balance' => (float) ($inv->balance ?? 0),
                    'balance_formatted' => $balance,
                    'total' => (float) ($inv->total ?? 0),
                    'display_label' => "{$invNum} - {$invDate} - {$status} - {$balance}",
                ];
            });
    }

    /**
     * API: Search existing patients by ID, Name, Phone, or Email.
     */
    public function searchPatients(Request $request): JsonResponse
    {
        $search = trim($request->query('q', ''));

        if (mb_strlen($search) < 2) {
            return response()->json([]);
        }

        $term = mb_strtolower($search, 'UTF-8');

        $patients = Patient::query()
            ->where(function ($query) use ($term, $search) {
                $query->whereRaw('LOWER(firstname) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(lastname) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw("LOWER(CONCAT(COALESCE(firstname, ''), ' ', COALESCE(lastname, ''))) LIKE ?", ["%{$term}%"])
                    ->orWhereRaw('LOWER(phone) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$term}%"]);

                if (is_numeric($search)) {
                    $query->orWhere('id_number', (int) $search)
                        ->orWhere('id', (int) $search);
                }
            })
            ->latest('id')
            ->take(10)
            ->get();

        return response()->json($patients);
    }

    /**
     * API: Quick Create Patient from modal.
     */
    public function quickCreatePatient(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'firstname' => ['nullable', 'string', 'max:50'],
            'lastname' => ['nullable', 'string', 'max:50'],
            'full_name' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:10'],
            'sex' => ['nullable', 'string', 'max:10'],
            'dob' => ['nullable', 'date'],
            'date_of_birth' => ['nullable', 'date'],
            'profession' => ['nullable', 'string', 'max:255'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
        ]);

        $firstname = trim($validated['firstname'] ?? '');
        $lastname = trim($validated['lastname'] ?? '');

        if ($firstname === '' && $lastname === '' && !empty($validated['full_name'])) {
            $parts = explode(' ', trim($validated['full_name']), 2);
            $firstname = $parts[0] ?? '';
            $lastname = $parts[1] ?? '';
        }

        $dob = $validated['dob'] ?? $validated['date_of_birth'] ?? null;
        $gender = $validated['gender'] ?? $validated['sex'] ?? 'male';
        $profession = $validated['profession'] ?? $validated['occupation'] ?? null;

        $age = null;
        if ($dob) {
            try {
                $age = Carbon::parse($dob)->age;
            } catch (\Throwable $e) {}
        }

        $patient = Patient::create([
            'firstname' => $firstname ?: 'New',
            'lastname' => $lastname ?: 'Patient',
            'gender' => $gender,
            'dob' => $dob,
            'age' => $age,
            'phone' => $validated['phone'] ?? null,
            'profession' => $profession,
            'address' => $validated['address'] ?? null,
        ]);

        return response()->json($patient, 201);
    }
}
