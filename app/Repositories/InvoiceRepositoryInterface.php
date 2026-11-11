<?php

namespace App\Repositories;

interface InvoiceRepositoryInterface
{
    public function getDataTable();

    public function columnsDataTable($table);

    public function find($id, array $columns = ['*']);

    public function findWithDetails($id);

    public function generateNextInvoiceNumber(?int $prefixId = null, ?string $dailyDate = null): array;

    public function createInvoice(array $data);

    public function updateInvoice($id, array $data);

    public function deleteInvoice($id);
}
