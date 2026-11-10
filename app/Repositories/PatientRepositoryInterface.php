<?php

namespace App\Repositories;

interface PatientRepositoryInterface
{
    public function getDataTable();

    public function columnsDataTable($table);

    public function getNextIdNumber(): int;

    public function find($id, array $columns = ['*']);

    public function findWithDetails($id);

    public function create(array $attributes = []);

    public function update($id, array $attributes = []);

    public function delete($id);
}
