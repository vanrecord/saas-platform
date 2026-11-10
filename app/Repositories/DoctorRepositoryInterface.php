<?php

namespace App\Repositories;

use App\Models\Tenant\Doctor;
use Illuminate\Http\UploadedFile;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

interface DoctorRepositoryInterface
{
    public function getDataTable();

    public function columnsDataTable(InertiaTable $table): void;

    public function find(int $id): ?Doctor;

    public function findWithRelations(int $id): ?Doctor;

    public function createDoctor(array $data, ?UploadedFile $avatar = null): Doctor;

    public function updateDoctor(Doctor $doctor, array $data, ?UploadedFile $avatar = null): Doctor;

    public function deleteDoctor(Doctor $doctor): bool;

    public function getSpecialties(): array;
}
