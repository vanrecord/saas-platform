<?php

namespace App\Repositories;

use App\Models\UserTenant;
use Illuminate\Http\UploadedFile;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

interface TenantUserRepositoryInterface
{
    public function getDataTable();

    public function columnsDataTable(InertiaTable $table): void;

    public function find(int $id): ?UserTenant;

    public function findWithProfile(int $id): ?UserTenant;

    public function createUser(array $data, ?UploadedFile $avatar = null): UserTenant;

    public function updateUser(int $id, array $data, ?UploadedFile $avatar = null, bool $removeAvatar = false): ?UserTenant;

    public function deleteUser(int $id): bool;

    public function getRoles(): array;
}
