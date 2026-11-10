<?php

namespace App\Repositories;

use App\Models\Tenant\Role;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

interface RoleRepositoryInterface
{
    public function getDataTable();

    public function columnsDataTable(InertiaTable $table);

    public function findWithPermissions(int $id): ?Role;

    public function createRole(array $data): Role;

    public function updateRole(int $id, array $data): ?Role;

    public function deleteRole(int $id): bool;

    public function getAvailablePermissions(): array;
}
