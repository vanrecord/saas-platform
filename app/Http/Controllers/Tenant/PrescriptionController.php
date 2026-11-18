<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\PrescriptionRequest;
use App\Models\Tenant\MedicalHistory;
use App\Models\Tenant\Medicine;
use App\Models\Tenant\Patient;
use App\Models\Tenant\Prescription;
use App\Models\Tenant\PrescriptionDetail;
use App\Repositories\PrescriptionRepositoryInterface;
use App\Supports\Trait\ClinicInfoTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class PrescriptionController extends Controller
{
    use ClinicInfoTrait;

    protected PrescriptionRepositoryInterface $prescriptionRepository;

    public function __construct(PrescriptionRepositoryInterface $prescriptionRepository)
    {
        $this->prescriptionRepository = $prescriptionRepository;
    }

    public function index(Request $request): Response
    {
        abort_if(
            !auth()->user()?->can('prescription:view') && !auth()->user()?->can('prescriptions:prescriptions:view'),
            403,
            'Unauthorized to view prescriptions.'
        );

        $prescriptions = $this->prescriptionRepository->getDataTable();

        $medicines = Medicine::orderBy('name')->get(['id', 'name']);
        $patients = Patient::orderBy('firstname')
            ->orderBy('lastname')
            ->get(['id', 'firstname', 'lastname', 'id_number']);

        return Inertia::render('Tenant/Prescription/Index', [
            'prescriptions' => $prescriptions,
            'medicines' => $medicines,
            'patients' => $patients,
        ])->table(function (InertiaTable $table) {
            $this->prescriptionRepository->columnsDataTable($table);
        });
    }

    public function store(PrescriptionRequest $request): RedirectResponse
    {
        abort_if(
            !auth()->user()?->can('prescription:create') &&
            !auth()->user()?->can('prescription:save') &&
            !auth()->user()?->can('prescriptions:prescriptions:create'),
            403,
            'Unauthorized to create prescriptions.'
        );

        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $prescription = Prescription::create([
                'patient_id' => $validated['patient_id'],
                'medical_history_id' => $validated['medical_history_id'] ?? null,
                'user_id' => auth()->id(),
                'prescription' => $validated['prescription'] ?? null,
            ]);

            $summaryParts = [];

            foreach ($validated['details'] as $detail) {
                $qty = (float) ($detail['qty'] ?? 1);
                $duration = (float) ($detail['ud'] ?? 1);
                $dose = (float) ($detail['dose'] ?? 1);
                $morning = !empty($detail['morning']);
                $afternoon = !empty($detail['afternoon']);
                $evening = !empty($detail['evening']);
                $night = !empty($detail['night']);

                $times = ($morning ? 1 : 0) + ($afternoon ? 1 : 0) + ($evening ? 1 : 0) + ($night ? 1 : 0);
                $totalPills = !empty($prescription->medical_history_id)
                    ? $qty
                    : $qty * ($times > 0 ? $times : 1) * $duration;

                $prescriptionDetail = PrescriptionDetail::create([
                    'prescription_id' => $prescription->id,
                    'medicine_id' => $detail['medicine_id'],
                    'dose' => $dose,
                    'duration' => $duration,
                    'dose_amount' => $qty,
                    'unit_dose' => $duration,
                    'morning' => $morning,
                    'afternoon' => $afternoon,
                    'evening' => $evening,
                    'night' => $night,
                    'note' => $detail['note'] ?? null,
                ]);

                $med = Medicine::find($detail['medicine_id']);
                $medName = $med?->name ?: ('Medicine #' . $detail['medicine_id']);
                $summaryParts[] = "{$medName} ({$totalPills} គ្រាប់)";
            }

            if (empty($prescription->prescription) && !empty($summaryParts)) {
                $prescription->updateQuietly([
                    'prescription' => implode(', ', $summaryParts),
                ]);
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Prescription created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create prescription: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show($id): JsonResponse
    {
        abort_if(
            !auth()->user()?->can('prescription:view') && !auth()->user()?->can('prescriptions:prescriptions:view'),
            403,
            'Unauthorized.'
        );

        $prescription = Prescription::with(['patient', 'prescriptionDetails.medicine', 'medicalHistory'])
            ->findOrFail($id);

        return response()->json($prescription);
    }

    public function update(PrescriptionRequest $request, $id): RedirectResponse
    {
        abort_if(
            !auth()->user()?->can('prescription:edit') &&
            !auth()->user()?->can('prescription:save') &&
            !auth()->user()?->can('prescriptions:prescriptions:edit'),
            403,
            'Unauthorized to edit prescriptions.'
        );

        $prescription = Prescription::findOrFail($id);

        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $prescription->update([
                'patient_id' => $validated['patient_id'],
                'medical_history_id' => $validated['medical_history_id'] ?? null,
                'prescription' => $validated['prescription'] ?? null,
            ]);

            // Replace existing details with updated ones
            $prescription->prescriptionDetails()->delete();

            $summaryParts = [];

            foreach ($validated['details'] as $detail) {
                $qty = (float) ($detail['qty'] ?? 1);
                $duration = (float) ($detail['ud'] ?? 1);
                $dose = (float) ($detail['dose'] ?? 1);
                $morning = !empty($detail['morning']);
                $afternoon = !empty($detail['afternoon']);
                $evening = !empty($detail['evening']);
                $night = !empty($detail['night']);

                $times = ($morning ? 1 : 0) + ($afternoon ? 1 : 0) + ($evening ? 1 : 0) + ($night ? 1 : 0);
                $totalPills = !empty($prescription->medical_history_id)
                    ? $qty
                    : $qty * ($times > 0 ? $times : 1) * $duration;

                PrescriptionDetail::create([
                    'prescription_id' => $prescription->id,
                    'medicine_id' => $detail['medicine_id'],
                    'dose' => $dose,
                    'duration' => $duration,
                    'dose_amount' => $qty,
                    'unit_dose' => $duration,
                    'morning' => $morning,
                    'afternoon' => $afternoon,
                    'evening' => $evening,
                    'night' => $night,
                    'note' => $detail['note'] ?? null,
                ]);

                $med = Medicine::find($detail['medicine_id']);
                $medName = $med?->name ?: ('Medicine #' . $detail['medicine_id']);
                $summaryParts[] = "{$medName} ({$totalPills} គ្រាប់)";
            }

            if (empty($validated['prescription']) && !empty($summaryParts)) {
                $prescription->updateQuietly([
                    'prescription' => implode(', ', $summaryParts),
                ]);
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Prescription updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update prescription: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy($id): RedirectResponse
    {
        abort_if(
            !auth()->user()?->can('prescription:delete') && !auth()->user()?->can('prescriptions:prescriptions:delete'),
            403,
            'Unauthorized to delete prescriptions.'
        );

        $prescription = Prescription::findOrFail($id);

        DB::beginTransaction();
        try {
            $prescription->prescriptionDetails()->delete();
            $prescription->delete();

            DB::commit();

            return redirect()->route('prescriptions.index')
                ->with('success', 'Prescription deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('prescriptions.index')
                ->with('error', 'Failed to delete prescription: ' . $e->getMessage());
        }
    }

    public function print($id): Response
    {
        abort_if(
            !auth()->user()?->can('prescription:view') &&
            !auth()->user()?->can('prescription:edit') &&
            !auth()->user()?->can('prescriptions:prescriptions:view'),
            403,
            'Unauthorized.'
        );

        $prescription = Prescription::with([
            'patient',
            'prescriptionDetails.medicine',
            'medicalHistory.doctor.user.profile',
            'user',
        ])->findOrFail($id);

        $clinic = $this->getClinicInfo();
        $patient = $prescription->patient;

        // Patient information
        $patientName = $patient?->full_name ?: 'N/A';
        $patientSex = $patient?->gender ?: $patient?->sex ?: 'M';
        $patientAge = $patient?->age ?: ($patient?->dob ? Carbon::parse($patient->dob)->age : '36');
        $patientId = $patient ? str_pad((string) ($patient->id_number ?: $patient->id), 6, '0', STR_PAD_LEFT) : '000567';

        // Doctor information
        $doctorName = $prescription->medicalHistory?->doctor?->full_name
            ?: $prescription->user?->name
            ?: auth()->user()?->name
            ?: 'fwta owner';

        // Medicines list
        $items = [];
        foreach ($prescription->prescriptionDetails as $idx => $detail) {
            $medName = $detail->medicine?->name ?: ('Medicine #' . $detail->medicine_id);
            $doseStr = $detail->dose ? ' ' . (int) $detail->dose : '';
            $fullName = $medName . $doseStr;

            $items[] = [
                'n' => $idx + 1,
                'name' => $fullName,
                'quantity' => ((int) $detail->total_calculated) . ' គ្រាប់',
                'usage' => $detail->usage_text,
                'days' => ((int) ($detail->duration ?: ($detail->unit_dose ?: 1))) . ' ថ្ងៃ',
                'note' => $detail->note ?: '',
            ];
        }

        return Inertia::render('Tenant/Prescription/Print', [
            'prescription' => [
                'id' => $prescription->id,
                'date' => $prescription->created_at ? Carbon::parse($prescription->created_at)->format('d/m/Y') : Carbon::today()->format('d/m/Y'),
                'patient' => [
                    'name' => $patientName,
                    'sex' => $patientSex,
                    'age' => $patientAge,
                    'id' => $patientId,
                ],
                'doctor' => [
                    'name' => $doctorName,
                ],
                'items' => $items,
            ],
            'clinic' => $clinic,
        ]);
    }

    public function patientHistories($patientId): JsonResponse
    {
        $histories = MedicalHistory::where('patient_id', $patientId)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get(['id', 'invoice_number', 'date', 'status', 'prefix_invoice', 'invoice_id'])
            ->map(function ($mh) {
                $label = $mh->invoice_number_formatted ?: ('Invoice #' . $mh->id);
                $date = $mh->date ? Carbon::parse($mh->date)->format('Y-m-d') : '';
                return [
                    'id' => $mh->id,
                    'label' => $date ? "{$label} ({$date})" : $label,
                ];
            });

        return response()->json($histories);
    }
}
