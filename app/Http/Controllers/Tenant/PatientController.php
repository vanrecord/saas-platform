<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\PatientRequest;
use App\Repositories\PatientRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class PatientController extends Controller
{
    protected PatientRepositoryInterface $patient;

    public function __construct(PatientRepositoryInterface $patient)
    {
        $this->patient = $patient;
    }

    public function index(): Response
    {
        abort_if(!auth()->user()?->can('patient:view'), 403, 'Unauthorized to view patients.');

        $patients = $this->patient->getDataTable();
        $view = 'Tenant/Patient/Index';

        return Inertia::render($view, [
            'patients' => $patients,
        ])->table(function (InertiaTable $table) {
            $this->patient->columnsDataTable($table);
        });
    }

    public function create(): Response
    {
        abort_if(!auth()->user()?->can('patient:create'), 403, 'Unauthorized to create patients.');

        $suggestedIdNumber = $this->patient->getNextIdNumber();

        return Inertia::render('Tenant/Patient/Create', [
            'suggestedIdNumber' => $suggestedIdNumber,
        ]);
    }

    public function store(PatientRequest $request): RedirectResponse
    {
        abort_if(!auth()->user()?->can('patient:save'), 403, 'Unauthorized to save patients.');

        $data = $request->validated();

        // Treat Date of Birth as authoritative when provided; recalculate age
        if (!empty($data['dob'])) {
            try {
                $data['age'] = Carbon::parse($data['dob'])->age;
            } catch (\Throwable $e) {
                // Ignore parse errors
            }
        } elseif (isset($data['age']) && $data['age'] !== null && $data['age'] !== '') {
            $data['dob'] = Carbon::today()->subYearsNoOverflow((int) $data['age'])->format('Y-m-d');
        }

        // Convert booleans
        $data['card_activated'] = $request->boolean('card_activated');
        $data['is_trail_card'] = $request->boolean('is_trail_card');

        // Normalize gender to M or F
        if (!empty($data['gender'])) {
            $first = strtoupper(substr(trim((string) $data['gender']), 0, 1));
            $data['gender'] = $first === 'M' ? 'M' : ($first === 'F' ? 'F' : $data['gender']);
        }

        // Handle avatar upload (keep filename short due to varchar(50) limit)
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = 'p_' . time() . '_' . Str::lower(Str::random(6)) . '.' . $extension;
            $file->storeAs('avatars', $filename, 'public');
            $data['avatar'] = 'avatars/' . $filename;
        } else {
            unset($data['avatar']);
        }

        unset($data['remove_avatar'], $data['return_to']);

        $patient = $this->patient->create($data);

        return redirect()->route('patients.index')
            ->with('success', 'Patient record created successfully.');
    }

    public function show($id): Response
    {
        abort_if(!auth()->user()?->can('patient:view'), 403, 'Unauthorized to view patients.');

        $patient = $this->patient->findWithDetails($id);

        if (!$patient) {
            abort(404, 'Patient not found');
        }

        return Inertia::render('Tenant/Patient/Show', [
            'patient' => $patient,
        ]);
    }

    public function edit($id): Response
    {
        abort_if(!auth()->user()?->can('patient:edit'), 403, 'Unauthorized to edit patients.');

        $patient = $this->patient->find($id);

        if (!$patient) {
            abort(404, 'Patient not found');
        }

        return Inertia::render('Tenant/Patient/Edit', [
            'patient' => $patient,
        ]);
    }

    public function update(PatientRequest $request, $id): RedirectResponse
    {
        abort_if(!auth()->user()?->can('patient:save'), 403, 'Unauthorized to save patients.');

        $patient = $this->patient->find($id);

        if (!$patient) {
            abort(404, 'Patient not found');
        }

        $data = $request->validated();

        // Treat Date of Birth as authoritative when provided; recalculate age
        if (!empty($data['dob'])) {
            try {
                $data['age'] = Carbon::parse($data['dob'])->age;
            } catch (\Throwable $e) {
                // Ignore parse errors
            }
        } elseif (isset($data['age']) && $data['age'] !== null && $data['age'] !== '') {
            $data['dob'] = Carbon::today()->subYearsNoOverflow((int) $data['age'])->format('Y-m-d');
        }

        // Convert booleans
        $data['card_activated'] = $request->boolean('card_activated');
        $data['is_trail_card'] = $request->boolean('is_trail_card');

        // Normalize gender to M or F
        if (!empty($data['gender'])) {
            $first = strtoupper(substr(trim((string) $data['gender']), 0, 1));
            $data['gender'] = $first === 'M' ? 'M' : ($first === 'F' ? 'F' : $data['gender']);
        }

        // Handle avatar upload or removal
        if ($request->hasFile('avatar')) {
            if ($patient->avatar && Storage::disk('public')->exists($patient->avatar)) {
                Storage::disk('public')->delete($patient->avatar);
            }
            $file = $request->file('avatar');
            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = 'p_' . time() . '_' . Str::lower(Str::random(6)) . '.' . $extension;
            $file->storeAs('avatars', $filename, 'public');
            $data['avatar'] = 'avatars/' . $filename;
        } elseif ($request->boolean('remove_avatar')) {
            if ($patient->avatar && Storage::disk('public')->exists($patient->avatar)) {
                Storage::disk('public')->delete($patient->avatar);
            }
            $data['avatar'] = null;
        } else {
            unset($data['avatar']);
        }

        unset($data['remove_avatar'], $data['return_to']);

        $this->patient->update($patient, $data);

        return redirect()->route('patients.index')
            ->with('success', 'Patient updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        abort_if(!auth()->user()?->can('patient:delete'), 403, 'Unauthorized to delete patients.');

        $patient = $this->patient->find($id);

        if ($patient) {
            if ($patient->avatar && Storage::disk('public')->exists($patient->avatar)) {
                Storage::disk('public')->delete($patient->avatar);
            }

            $this->patient->delete($patient);
        }

        return redirect()->route('patients.index')
            ->with('success', 'Patient deleted successfully.');
    }
}
