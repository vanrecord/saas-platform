<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\PatientRequest;
use App\Models\Tenant\Patient;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PatientController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Patient::query()->withCount(['dentalCharts']);

        // Search filter
        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $term = mb_strtolower($search, 'UTF-8');

            $query->where(function ($q) use ($term, $search) {
                $q->whereRaw('LOWER(firstname) LIKE ?', ["%{$term}%"])
                  ->orWhereRaw('LOWER(lastname) LIKE ?', ["%{$term}%"])
                  ->orWhereRaw("LOWER(CONCAT(COALESCE(firstname, ''), ' ', COALESCE(lastname, ''))) LIKE ?", ["%{$term}%"])
                  ->orWhereRaw('LOWER(phone) LIKE ?', ["%{$term}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$term}%"]);

                if (is_numeric($search)) {
                    $q->orWhere('id_number', (int) $search)
                      ->orWhere('id', (int) $search);
                }
            });
        }

        // Sorting
        $allowedSorts = [
            'id', 'firstname', 'lastname', 'gender', 'age', 'dob',
            'id_number', 'balance', 'card_activated', 'created_at',
        ];

        $sortBy = $request->query('sort_by', 'id');
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'id';
        }

        $query->orderBy($sortBy, $sortDir);

        $patients = $query->paginate(15)->withQueryString();

        return Inertia::render('Tenant/Patient/Index', [
            'patients' => $patients,
            'filters' => [
                'search' => $request->query('search', ''),
                'sort_by' => $sortBy,
                'sort_dir' => $sortDir,
            ],
        ]);
    }

    public function create(): Response
    {
        $suggestedIdNumber = Patient::getNextIdNumber();

        return Inertia::render('Tenant/Patient/Create', [
            'suggestedIdNumber' => $suggestedIdNumber,
        ]);
    }

    public function store(PatientRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Calculate age from dob if not explicitly entered
        if (!isset($data['age']) && !empty($data['dob'])) {
            try {
                $data['age'] = Carbon::parse($data['dob'])->age;
            } catch (\Throwable $e) {
                // Ignore parse errors
            }
        }

        // Convert booleans
        $data['card_activated'] = $request->boolean('card_activated');
        $data['is_trail_card'] = $request->boolean('is_trail_card');

        // Handle avatar upload (keep filename short due to varchar(50) limit)
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = 'p_' . time() . '_' . Str::lower(Str::random(6)) . '.' . $extension;
            $file->storeAs('avatars', $filename, 'public');
            $data['avatar'] = 'avatars/' . $filename; // e.g. avatars/p_1730000000_a1b2c3.jpg (30 chars <= 50)
        } else {
            unset($data['avatar']);
        }

        unset($data['remove_avatar'], $data['return_to']);

        $patient = Patient::create($data);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Patient record created successfully.');
    }

    public function show(Patient $patient): Response
    {
        $patient->load([
            'dentalCharts' => fn ($q) => $q->withCount('teeth')->latest('chart_date'),
        ]);

        return Inertia::render('Tenant/Patient/Show', [
            'patient' => $patient,
        ]);
    }

    public function edit(Patient $patient): Response
    {
        return Inertia::render('Tenant/Patient/Edit', [
            'patient' => $patient,
        ]);
    }

    public function update(PatientRequest $request, Patient $patient): RedirectResponse
    {
        $data = $request->validated();

        // Calculate age from dob if not explicitly entered
        if (!isset($data['age']) && !empty($data['dob'])) {
            try {
                $data['age'] = Carbon::parse($data['dob'])->age;
            } catch (\Throwable $e) {
                // Ignore parse errors
            }
        }

        // Convert booleans
        $data['card_activated'] = $request->boolean('card_activated');
        $data['is_trail_card'] = $request->boolean('is_trail_card');

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

        $patient->update($data);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        if ($patient->avatar && Storage::disk('public')->exists($patient->avatar)) {
            Storage::disk('public')->delete($patient->avatar);
        }

        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Patient deleted successfully.');
    }
}
