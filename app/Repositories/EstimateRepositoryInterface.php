<?php

namespace App\Repositories;

interface EstimateRepositoryInterface
{
    public function getDataTable();

    public function columnsDataTable($table);

    public function find($id, array $columns = ['*']);

    public function findWithDetails($id);

    public function getNextCode(): int;

    public function createEstimate(array $data);

    public function updateEstimate($id, array $data);

    public function deleteEstimate($id);

    public function approveItem(int $quoteId, int $itemId);

    public function rejectItem(int $quoteId, int $itemId, ?string $reason = null);

    public function addItem(int $quoteId, array $itemData);
}
