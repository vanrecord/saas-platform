<?php

namespace App\Repositories;

use App\Models\Tenant\PaymentMethod;
use Illuminate\Http\UploadedFile;

interface PaymentMethodRepositoryInterface
{
    public function getDataTable();

    public function columnsDataTable($table);

    public function find($id, array $columns = ['*']);

    public function createPaymentMethod(array $data, ?UploadedFile $qrCode = null): PaymentMethod;

    public function updatePaymentMethod(PaymentMethod $paymentMethod, array $data, ?UploadedFile $qrCode = null, bool $removeQrCode = false): PaymentMethod;

    public function deletePaymentMethod(PaymentMethod $paymentMethod): bool;

    public function getNextOrder(): int;
}
