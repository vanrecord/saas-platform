<?php

namespace App\Repositories;

use App\Models\Tenant\MedicalHistory;
use App\Models\Tenant\Patient;
use App\Models\Tenant\Payment;
use App\Models\Tenant\PaymentMethod;
use App\Repositories\Base\BaseRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(Payment::class)
             ->setRepositoryId('rinvex.repository.uniqueid');
    }

    public function getDataTable()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            $term = strtolower(trim((string) $value));
            if ($term === '') {
                return;
            }

            $query->where(function ($q) use ($term, $value) {
                $q->where(DB::raw('LOWER(COALESCE(txnref, \'\'))'), 'LIKE', "%{$term}%")
                  ->orWhere(DB::raw('LOWER(COALESCE(currency, \'\'))'), 'LIKE', "%{$term}%")
                  ->orWhere(DB::raw('LOWER(COALESCE(status, \'\'))'), 'LIKE', "%{$term}%")
                  ->orWhereHas('medicalHistory', function ($mQuery) use ($term) {
                      $mQuery->where(DB::raw('LOWER(COALESCE(invoice_number, \'\'))'), 'LIKE', "%{$term}%")
                             ->orWhere(DB::raw('LOWER(COALESCE(prefix_invoice, \'\'))'), 'LIKE', "%{$term}%");
                  })
                  ->orWhereHas('patient', function ($pQuery) use ($term) {
                      $pQuery->where(DB::raw('LOWER(COALESCE(firstname, \'\'))'), 'LIKE', "%{$term}%")
                             ->orWhere(DB::raw('LOWER(COALESCE(lastname, \'\'))'), 'LIKE', "%{$term}%")
                             ->orWhere(DB::raw("LOWER(CONCAT(COALESCE(firstname, ''), ' ', COALESCE(lastname, '')))"), 'LIKE', "%{$term}%")
                             ->orWhere('phone', 'LIKE', "%{$term}%");
                  })
                  ->orWhereHas('paymentMethod', function ($pmQuery) use ($term) {
                      $pmQuery->where(DB::raw('LOWER(COALESCE(name, \'\'))'), 'LIKE', "%{$term}%");
                  });

                if (is_numeric($value)) {
                    $q->orWhere('amount', (float) $value)
                      ->orWhere('amount_kh', (int) $value)
                      ->orWhere('patient_id', (int) $value)
                      ->orWhere('medical_history_id', (int) $value)
                      ->orWhere('id', (int) $value);
                }
            });
        });

        $startDateFilter = AllowedFilter::callback('start_date', function ($query, $value) {
            if (!empty($value)) {
                $query->whereDate('date', '>=', Carbon::parse($value)->startOfDay());
            }
        });

        $endDateFilter = AllowedFilter::callback('end_date', function ($query, $value) {
            if (!empty($value)) {
                $query->whereDate('date', '<=', Carbon::parse($value)->endOfDay());
            }
        });

        $dateRangeFilter = AllowedFilter::callback('date_range', function ($query, $value) {
            if (is_array($value)) {
                $start = $value['start'] ?? $value[0] ?? null;
                $end = $value['end'] ?? $value[1] ?? null;
            } else {
                $parts = explode(',', (string) $value);
                $start = $parts[0] ?? null;
                $end = $parts[1] ?? null;
            }

            if ($start) {
                $query->whereDate('date', '>=', Carbon::parse($start)->startOfDay());
            }
            if ($end) {
                $query->whereDate('date', '<=', Carbon::parse($end)->endOfDay());
            }
        });

        $invoiceFilter = AllowedFilter::callback('invoice_number', function ($query, $value) {
            $term = strtolower(trim((string) $value));
            if ($term !== '') {
                $query->whereHas('medicalHistory', function ($q) use ($term) {
                    $q->where(DB::raw('LOWER(COALESCE(invoice_number, \'\'))'), 'LIKE', "%{$term}%")
                      ->orWhere(DB::raw('LOWER(COALESCE(prefix_invoice, \'\'))'), 'LIKE', "%{$term}%");

                    if (is_numeric($term)) {
                        $q->orWhere('id', (int) $term)
                          ->orWhere('invoice_id', (int) $term)
                          ->orWhere('invoice_num_year_series', (int) $term);
                    }
                });
            }
        });

        $statusFilter = AllowedFilter::callback('status', function ($query, $value) {
            $val = strtolower(trim((string) $value));
            if ($val !== '' && $val !== 'all') {
                $query->where(DB::raw('LOWER(status)'), $val);
            }
        });

        return QueryBuilder::for(Payment::class)
            ->with([
                'patient',
                'medicalHistory',
                'paymentMethod',
                'verifiedBy.profile',
            ])
            ->defaultSort('-date')
            ->allowedFields([
                'id',
                'date',
                'patient_id',
                'medical_history_id',
                'payment_method_id',
                'user_id',
                'amount',
                'amount_kh',
                'currency',
                'txnref',
                'status',
                'created_at',
                'updated_at',
            ])
            ->allowedSorts([
                'id',
                'date',
                'amount',
                'amount_kh',
                'currency',
                'txnref',
                'status',
                'created_at',
            ])
            ->allowedFilters([
                AllowedFilter::exact('patient_id'),
                AllowedFilter::exact('payment_method_id'),
                $startDateFilter,
                $endDateFilter,
                $dateRangeFilter,
                $invoiceFilter,
                $statusFilter,
                $globalSearch,
            ])
            ->paginate()
            ->withQueryString();
    }

    public function columnsDataTable($table)
    {
        $table->perPageOptions(config('core.per_page_option') ?? [10, 15, 30, 50, 100]);
        $table->withGlobalSearch();
        $table->column(key: 'actions', label: 'Action');
        $table->column(key: 'date', label: 'Date', sortable: true);
        $table->column(key: 'patient_name', label: 'Patient');
        $table->column(key: 'invoice_number', label: 'Invoice #', searchable: true);
        $table->column(key: 'payment_method_name', label: 'Payment Method');
        $table->column(key: 'txnref', label: 'txnref', sortable: true, searchable: true);
        $table->column(key: 'amount', label: 'Amount ($)', sortable: true);
        $table->column(key: 'amount_kh', label: 'Amount (៛)', sortable: true);
        $table->column(key: 'currency', label: 'Paid Currency', sortable: true);
        $table->column(key: 'verified_by_name', label: 'Verified By');
        $table->column(key: 'status', label: 'Status', sortable: true);
    }

    public function searchPatients(string $term, int $limit = 20)
    {
        $term = trim($term);
        if (mb_strlen($term) < 3) {
            return collect();
        }

        $lower = strtolower($term);

        return Patient::query()
            ->where(function ($q) use ($lower, $term) {
                $q->where(DB::raw('LOWER(COALESCE(firstname, \'\'))'), 'LIKE', "%{$lower}%")
                  ->orWhere(DB::raw('LOWER(COALESCE(lastname, \'\'))'), 'LIKE', "%{$lower}%")
                  ->orWhere(DB::raw("LOWER(CONCAT(COALESCE(firstname, ''), ' ', COALESCE(lastname, '')))"), 'LIKE', "%{$lower}%")
                  ->orWhere('phone', 'LIKE', "%{$term}%");

                if (is_numeric($term)) {
                    $q->orWhere('id_number', (int) $term)
                      ->orWhere('id', (int) $term);
                }
            })
            ->select('id', 'id_number', 'firstname', 'lastname', 'phone')
            ->orderBy('firstname')
            ->limit($limit)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'id_number' => $p->id_number,
                    'firstname' => $p->firstname,
                    'lastname' => $p->lastname,
                    'full_name' => trim($p->firstname . ' ' . $p->lastname) ?: 'Patient #' . ($p->id_number ?: $p->id),
                    'phone' => $p->phone,
                ];
            });
    }

    public function deletePayment(int $id): array
    {
        return DB::transaction(function () use ($id) {
            $payment = Payment::with('medicalHistory')->findOrFail($id);

            // Business rule: Do not delete a refund or a payment that corrupts refund history
            if ($payment->amount < 0 || strtolower($payment->status) === 'refunded') {
                return [
                    'success' => false,
                    'message' => 'Unable to delete this payment because it is a refund record.',
                ];
            }

            $invoice = $payment->medicalHistory;

            // Soft delete payment
            $payment->delete();

            // Recalculate invoice financial calculations
            if ($invoice) {
                $payments = Payment::where('medical_history_id', $invoice->id)->get();
                $paidUsd = (float) $payments->where('amount', '>', 0)->sum('amount');
                $paidKh = (float) $payments->where('amount_kh', '>', 0)->sum('amount_kh');
                $refunded = (float) $payments->where('amount', '<', 0)->sum(function ($p) {
                    return abs((float) $p->amount);
                });
                $balance = max(0, $invoice->total - $paidUsd + $refunded);

                $status = MedicalHistory::determineStatus((float) $invoice->total, $paidUsd, $refunded, $balance);

                $invoice->update([
                    'paid_usd' => $paidUsd,
                    'paid_kh' => $paidKh,
                    'refunded' => $refunded,
                    'balance' => $balance,
                    'status' => $status,
                ]);
            }

            return [
                'success' => true,
                'message' => 'Payment deleted successfully.',
            ];
        });
    }

    public function findForReceipt(int $id)
    {
        return Payment::with([
            'patient',
            'medicalHistory.doctor.user.profile',
            'paymentMethod',
            'verifiedBy.profile',
        ])->findOrFail($id);
    }
}
