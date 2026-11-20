<?php

namespace App\Repositories;

use App\Models\Tenant\Target;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

interface TargetRepositoryInterface
{
    public function getTarget(bool $byDoctor = false);

    public function getTargetAmountByDate($start = '', $end = '');

    public function getIncomeBy($startDate, $endDate, $doctorId = null): float;

    public function getClinicDataTable();

    public function getDoctorDataTable();

    public function columnsClinicDataTable(InertiaTable $table);

    public function columnsDoctorDataTable(InertiaTable $table);

    public function createClinicTarget(array $data): Target;

    public function updateClinicTarget(int $id, array $data): Target;

    public function createDoctorTarget(array $data): Target;

    public function updateDoctorTarget(int $id, array $data): Target;

    public function deleteTarget(int $id, bool $byDoctor): bool;
}
