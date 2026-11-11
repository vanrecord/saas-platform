<?php

namespace App\Repositories;

interface PaymentRepositoryInterface
{
    public function getDataTable();

    public function columnsDataTable($table);

    public function searchPatients(string $term, int $limit = 20);

    public function deletePayment(int $id): array;

    public function findForReceipt(int $id);
}
