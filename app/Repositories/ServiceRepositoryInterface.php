<?php

namespace App\Repositories;

use App\Models\Tenant\Service;
use Illuminate\Database\Eloquent\Collection;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

interface ServiceRepositoryInterface
{
    public function getDataTable();

    public function columnsDataTable(InertiaTable $table);

    public function getServiceCategories(): Collection;

    public function findWithDetails(int $id): ?Service;

    public function createService(array $data): Service;

    public function updateService(int $id, array $data): ?Service;

    public function deleteService(int $id): bool;

    public function getHospitalId(): int;
}
