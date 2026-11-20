<?php

namespace App\Repositories;

interface FundTransferRepositoryInterface
{
    public function getDataTable();

    public function columnsDataTable($table);

    public function getFundTransfer();

    public function getFundTransferDetail($patient_id);

    public function getIdNumber(): int;

    public function find($id, array $columns = ['*']);

    public function create(array $attributes = []);

    public function update($id, array $attributes = []);

    public function delete($id);

    public function verify($id);

    public function updateBalance($patientId, $amount);
}
