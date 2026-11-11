<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\PatientRequest;
use App\Models\Tenant\Patient;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PatientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Patient::query()->withCount(['medicalHistories']);

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

        $patients = $query->latest('id')->paginate($request->integer('per_page', 15));

        return response()->json($patients);
    }

    public function store(PatientRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (!isset($data['age']) && !empty($data['dob'])) {
            try {
                $data['age'] = Carbon::parse($data['dob'])->age;
            } catch (\Throwable $e) {
                // Ignore parse errors
            }
        }

        $data['card_activated'] = $request->boolean('card_activated');
        $data['is_trail_card'] = $request->boolean('is_trail_card');

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

        $patient = Patient::create($data);

        return response()->json([
            'message' => 'Patient created successfully',
            'data' => $patient,
        ], 201);
    }

    public function show(Patient $patient): JsonResponse
    {
        $patient->load([
            'medicalHistories' => fn ($q) => $q->latest('date'),
        ]);

        return response()->json(['data' => $patient]);
    }

    public function update(PatientRequest $request, Patient $patient): JsonResponse
    {
        $data = $request->validated();

        if (!isset($data['age']) && !empty($data['dob'])) {
            try {
                $data['age'] = Carbon::parse($data['dob'])->age;
            } catch (\Throwable $e) {
                // Ignore parse errors
            }
        }

        $data['card_activated'] = $request->boolean('card_activated');
        $data['is_trail_card'] = $request->boolean('is_trail_card');

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

        return response()->json([
            'message' => 'Patient updated successfully',
            'data' => $patient,
        ]);
    }

    public function destroy(Patient $patient): JsonResponse
    {
        if ($patient->avatar && Storage::disk('public')->exists($patient->avatar)) {
            Storage::disk('public')->delete($patient->avatar);
        }

        $patient->delete();

        return response()->json(['message' => 'Patient deleted successfully']);
    }
}
