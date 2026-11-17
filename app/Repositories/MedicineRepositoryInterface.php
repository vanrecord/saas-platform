<?php

namespace App\Repositories;

interface MedicineRepositoryInterface
{
    public function getDataTable();

    public function columnsDataTable($table);

    public function find($id, array $columns = ['*']);

    public function create(array $attributes = []);

    public function update($id, array $attributes = []);

    public function delete($id);
}
