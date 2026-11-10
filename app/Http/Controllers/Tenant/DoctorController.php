<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\DoctorRequest;
use App\Models\Tenant\Doctor;
use App\Repositories\DoctorRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class DoctorController extends Controller
{
    public function __construct(
        protected DoctorRepositoryInterface $doctorRepository
    ) {
    }

    protected function authorizePermission(string $permission): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Unauthorized.');
        }

        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return;
        }

        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return;
        }

        if (!$user->can($permission)) {
            abort(403, "Unauthorized: missing {$permission} permission.");
        }
    }

    public function index(): Response
    {
        $this->authorizePermission('doctor:view');

        $doctors = $this->doctorRepository->getDataTable();

        return Inertia::render('Tenant/Doctor/Index', [
            'doctors' => $doctors,
        ])->table(function (InertiaTable $table) {
            $this->doctorRepository->columnsDataTable($table);
        });
    }

    public function create(): Response
    {
        $this->authorizePermission('doctor:create');

        $specialties = $this->doctorRepository->getSpecialties();

        return Inertia::render('Tenant/Doctor/Create', [
            'specialties' => $specialties,
        ]);
    }

    public function store(DoctorRequest $request): RedirectResponse
    {
        $this->authorizePermission('doctor:save');

        $this->doctorRepository->createDoctor(
            $request->validated(),
            $request->file('avatar')
        );

        return redirect()->route('doctors.index')
            ->with('success', 'Doctor created successfully.');
    }

    public function show(int $id)
    {
        return redirect()->route('doctors.edit', $id);
    }

    public function edit(int $id): Response
    {
        $this->authorizePermission('doctor:edit');

        $doctor = $this->doctorRepository->findWithRelations($id);

        if (!$doctor) {
            abort(404, 'Doctor not found.');
        }

        $specialties = $this->doctorRepository->getSpecialties();
        $user = $doctor->user;
        $profile = $user?->profile;

        $doctorData = [
            'id' => $doctor->id,
            'username' => $user?->name ?? '',
            'email' => $user?->email ?? '',
            'firstname' => $profile?->firstname ?? '',
            'lastname' => $profile?->lastname ?? '',
            'date_of_birth' => $profile?->date_of_birth ? $profile->date_of_birth->format('Y-m-d') : '',
            'gender' => !empty($profile?->gender) ? (strtoupper(substr($profile->gender, 0, 1)) === 'M' ? 'M' : 'F') : 'F',
            'primary_mobile' => $profile?->primary_mobile ?? '',
            'secondary_mobile' => $profile?->secondary_mobile ?? '',
            'specialties' => $doctor->specialties->pluck('id')->toArray(),
            'description' => $profile?->description ?? '',
            'avatar_url' => $profile?->avatar_url ?? null,
        ];

        return Inertia::render('Tenant/Doctor/Edit', [
            'doctor' => $doctorData,
            'specialties' => $specialties,
        ]);
    }

    public function update(DoctorRequest $request, int $id): RedirectResponse
    {
        $this->authorizePermission('doctor:save');

        $doctor = $this->doctorRepository->find($id);

        if (!$doctor) {
            abort(404, 'Doctor not found.');
        }

        $this->doctorRepository->updateDoctor(
            $doctor,
            $request->validated(),
            $request->file('avatar')
        );

        return redirect()->route('doctors.index')
            ->with('success', 'Doctor updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->authorizePermission('doctor:delete');

        $doctor = $this->doctorRepository->find($id);

        if (!$doctor) {
            abort(404, 'Doctor not found.');
        }

        $this->doctorRepository->deleteDoctor($doctor);

        return redirect()->route('doctors.index')
            ->with('success', 'Doctor deleted successfully.');
    }
}
