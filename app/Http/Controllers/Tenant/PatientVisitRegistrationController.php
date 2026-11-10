<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Patient;
use App\Models\Tenant\PatientVisit;
use App\Models\UserTenant;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PatientVisitRegistrationController extends Controller
{
    /**
     * Display the Patient Visit Registration page (matching reference screenshot).
     */
    public function create(Request $request): Response
    {
        abort_if(!auth()->user()?->can('register:view'), 403, 'Unauthorized to view register.');

        $selectedPatient = null;
        if ($request->filled('patient_id')) {
            $selectedPatient = Patient::find($request->query('patient_id'));
        }

        // Retrieve doctors from tenant staff/users
        $doctors = UserTenant::select('id', 'name', 'email')->get();
        if ($doctors->isEmpty()) {
            $doctors = collect([
                ['id' => 1, 'name' => 'Dr. Sovann (Dental Surgeon)'],
                ['id' => 2, 'name' => 'Dr. Chan (Orthodontist)'],
                ['id' => 3, 'name' => 'Dr. Sophea (Pediatric Dentist)'],
                ['id' => 4, 'name' => 'Dr. Vicheka (Periodontist)'],
            ]);
        }

        return Inertia::render('Tenant/Visit/Register', [
            'selectedPatient' => $selectedPatient,
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
            'visit_type' => ['required', 'string', 'max:255'],
            'doctor_id' => ['nullable'],
            'doctor_name' => ['nullable', 'string', 'max:255'],
            'chief_complaint' => ['nullable', 'string', 'max:1000'],
            'clinical_notes' => ['nullable', 'string', 'max:2000'],
            'reception_type' => ['nullable', 'string', 'max:255'],
            'outstanding_balance' => ['nullable', 'numeric', 'min:0'],
        ]);

        $patient = Patient::findOrFail($validated['patient_id']);

        $visit = PatientVisit::create([
            'patient_id' => $patient->id,
            'visit_number' => PatientVisit::generateVisitNumber(),
            'workflow_option' => $validated['workflow_option'],
            'invoice_reference' => $validated['workflow_option'] === 'new_invoice'
                ? PatientVisit::generateInvoiceReference()
                : ($request->input('invoice_reference') ?? 'FOLLOWUP-' . date('Ymd')),
            'visit_type' => $validated['visit_type'],
            'doctor_id' => $validated['doctor_id'] ? (int) $validated['doctor_id'] : null,
            'doctor_name' => $validated['doctor_name'] ?? 'Attending Dentist',
            'chief_complaint' => $validated['chief_complaint'],
            'clinical_notes' => $validated['clinical_notes'],
            'reception_type' => $validated['reception_type'] ?? ($validated['workflow_option'] === 'new_invoice' ? 'Initial Visit (Checked In)' : 'Follow-up Consultation'),
            'outstanding_balance' => $validated['outstanding_balance'] ?? 0.00,
            'status' => 'checked_in',
            'visited_at' => Carbon::now(),
        ]);

        return redirect()->route('tenant.visit.create')
            ->with('success', "Patient {$patient->full_name} has been successfully registered for visit ({$visit->visit_number})!");
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

