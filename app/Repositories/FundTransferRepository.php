<?php

namespace App\Repositories;

use App\Models\Tenant\FundTransfer;
use App\Models\Tenant\Patient;
use App\Repositories\Base\BaseRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class FundTransferRepository extends BaseRepository implements FundTransferRepositoryInterface
{
    public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(FundTransfer::class)
             ->setRepositoryId('rinvex.repository.fund_transfer');
    }

    /**
     * Group deposits by patient.
     * Preserves legacy FundTransferRepository::getFundTransfer() business logic.
     */
    public function getFundTransfer()
    {
        return FundTransfer::query()
            ->join('patients', 'patients.id', '=', 'fund_transfers.patient_id')
            ->select(
                'fund_transfers.patient_id',
                'fund_transfers.currency',
                'patients.balance as remain',
                'patients.firstname',
                'patients.lastname',
                'patients.id_number',
                'patients.phone',
                DB::raw("max(fund_transfers.date) as date"),
                DB::raw("sum(CASE WHEN fund_transfers.is_verify = true THEN fund_transfers.amount ELSE 0 END) as amount"),
                DB::raw("CONCAT(COALESCE(patients.firstname, ''), ' ', COALESCE(patients.lastname, '')) AS fullname")
            )
            ->groupBy(
                'fund_transfers.patient_id',
                'fund_transfers.currency',
                'patients.balance',
                'patients.firstname',
                'patients.lastname',
                'patients.id_number',
                'patients.phone',
                'patients.id'
            );
    }

    /**
     * Get DataTable query object with filtering and pagination.
     */
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

            $query->havingRaw(
                "LOWER(CONCAT(COALESCE(patients.firstname, ''), ' ', COALESCE(patients.lastname, ''))) LIKE ?",
                ["%{$term}%"]
            )->orHavingRaw(
                "LOWER(COALESCE(fund_transfers.currency, '')) LIKE ?",
                ["%{$term}%"]
            );

            if (is_numeric($value)) {
                $query->orHavingRaw('patients.id_number = ?', [(int) $value])
                      ->orHavingRaw('fund_transfers.patient_id = ?', [(int) $value]);
            }
        });

        $patientFilter = AllowedFilter::callback('patient_id', function ($query, $value) {
            if (!empty($value)) {
                $query->where('fund_transfers.patient_id', (int) $value);
            }
        });

        $baseQuery = $this->getFundTransfer();

        $paginated = QueryBuilder::for($baseQuery)
            ->defaultSort('-date')
            ->allowedSorts([
                'fullname',
                'currency',
                'amount',
                'remain',
                'date',
            ])
            ->allowedFilters([
                $globalSearch,
                $patientFilter,
            ])
            ->paginate()
            ->withQueryString();

        // Calculate paid_deposit for each row
        $paginated->getCollection()->transform(function ($row) {
            $amount = (float) ($row->amount ?? 0);
            $remain = (float) ($row->remain ?? 0);
            $paid = max(0.0, (float) bcsub((string) $amount, (string) $remain, 2));

            $row->amount_formatted = number_format($amount, 2, '.', '');
            $row->remain_formatted = number_format($remain, 2, '.', '');
            $row->paid_deposit = number_format($paid, 2, '.', '');
            $row->date_formatted = $row->date ? Carbon::parse($row->date)->format('d/m/Y H:i') : '-';

            return $row;
        });

        return $paginated;
    }

    public function columnsDataTable($table)
    {
        $table->perPageOptions(config('core.per_page_option') ?? [10, 15, 30, 50, 100]);
        $table->withGlobalSearch();
        $table->column(key: 'actions', label: 'Action');
        $table->column(key: 'index', label: 'No.');
        $table->column(key: 'fullname', label: 'Patient', sortable: true);
        $table->column(key: 'currency', label: 'Currency', sortable: true);
        $table->column(key: 'amount', label: 'Amount', sortable: true);
        $table->column(key: 'remain', label: 'Remain', sortable: true);
        $table->column(key: 'paid_deposit', label: 'Total paid on deposit');
    }

    /**
     * Get all deposits belonging to a patient.
     */
    public function getFundTransferDetail($patient_id)
    {
        return FundTransfer::query()
            ->with(['patient', 'creator', 'verifier'])
            ->where('patient_id', $patient_id)
            ->orderByDesc('id')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => (int) $item->id,
                    'code' => $item->formatted_code,
                    'code_raw' => (int) $item->code,
                    'patient_id' => (int) $item->patient_id,
                    'patient_name' => $item->patient ? $item->patient->full_name : '',
                    'is_cash' => (bool) $item->is_cash,
                    'deposit_type' => $item->is_cash ? 'Cash' : 'Bank',
                    'status' => (int) $item->status,
                    'status_label' => $item->status_label,
                    'currency' => $item->currency ?: 'USD',
                    'amount' => (float) $item->amount,
                    'amount_formatted' => $item->amount_formatted,
                    'base_amount' => (float) $item->base_amount,
                    'date' => $item->date ? Carbon::parse($item->date)->format('Y-m-d') : '',
                    'date_formatted' => $item->date_formatted,
                    'is_verify' => (bool) $item->is_verify,
                    'verify_by' => $item->verify_by,
                    'verifier_name' => $item->verifier ? $item->verifier->name : null,
                    'creator_name' => $item->creator ? $item->creator->name : null,
                    'note' => $item->note ?? '',
                    'created_at' => $item->created_at ? $item->created_at->format('d/m/Y H:i') : '',
                ];
            });
    }

    /**
     * Generate next incremental deposit code.
     */
    public function getIdNumber(): int
    {
        $max = (int) FundTransfer::max('code');
        return $max > 0 ? $max + 1 : 1;
    }

    /**
     * Verify a cash deposit inside a DB transaction.
     * Prevents duplicate verification and updates patient balance.
     */
    public function verify($id): array
    {
        return DB::transaction(function () use ($id) {
            $fundTransfer = FundTransfer::where('id', $id)
                ->where('is_verify', false)
                ->lockForUpdate()
                ->first();

            if (!$fundTransfer) {
                return [
                    'success' => false,
                    'message' => 'The record you are looking for is not found or is already verified.',
                ];
            }

            // Update patient balance
            $patient = Patient::lockForUpdate()->find($fundTransfer->patient_id);
            if ($patient) {
                $patient->updateBalance((float) $fundTransfer->base_amount);
            }

            // Update status and verifier
            $fundTransfer->is_verify = true;
            $fundTransfer->verify_by = auth()->id();
            $fundTransfer->status = FundTransfer::VERIFIED;
            $fundTransfer->save();

            return [
                'success' => true,
                'message' => 'The deposit record was successfully verified.',
                'data' => $fundTransfer,
            ];
        });
    }

    /**
     * Safely delete unverified deposit.
     */
    public function delete($id): array
    {
        $fundTransfer = FundTransfer::find($id);

        if (!$fundTransfer) {
            return [
                'success' => false,
                'message' => 'The record you are looking for is not found.',
            ];
        }

        if ($fundTransfer->is_verify || $fundTransfer->status === FundTransfer::VERIFIED) {
            return [
                'success' => false,
                'message' => 'Verified deposits cannot be deleted.',
            ];
        }

        $fundTransfer->delete();

        return [
            'success' => true,
            'message' => 'The record was successfully deleted.',
        ];
    }

    /**
     * Update patient balance directly.
     */
    public function updateBalance($patientId, $amount)
    {
        $patient = Patient::find($patientId);
        if ($patient) {
            return $patient->updateBalance((float) $amount);
        }
        return false;
    }
}
