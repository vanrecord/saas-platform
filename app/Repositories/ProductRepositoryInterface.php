<?php

namespace App\Repositories;

use App\Models\Tenant\Product;
use Illuminate\Database\Eloquent\Collection;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

interface ProductRepositoryInterface
{
    public function getDataTable();

    public function columnsDataTable(InertiaTable $table);

    public function getSuppliers(): Collection;

    public function getProductCategories(): Collection;

    public function findWithDetails(int $id): ?Product;

    public function createProduct(array $data): Product;

    public function updateProduct(int $id, array $data): ?Product;

    public function deleteProduct(int $id): bool;

    public function getHospitalId(): int;
}
