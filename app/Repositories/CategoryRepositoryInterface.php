<?php

namespace App\Repositories;

use App\Models\Tenant\Category;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

interface CategoryRepositoryInterface
{
    public function getDataTable(string $type);

    public function columnsDataTable(InertiaTable $table);

    public function findForType(int $id, string $type): ?Category;

    public function createForType(string $type, array $data): Category;

    public function updateForType(int $id, string $type, array $data): ?Category;

    public function deleteForType(int $id, string $type): bool;

    public function getHospitalId(): int;
}
