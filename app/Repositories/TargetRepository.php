<?php

namespace App\Repositories;

use App\Models\Tenant\MedicalHistory;
use App\Models\Tenant\Target;
use App\Models\UserTenant;
use App\Repositories\Base\BaseRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\DB;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TargetRepository extends BaseRepository implements TargetRepositoryInterface
{
    public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(Target::class)
             ->setRepositoryId('tenant.target.repository');
    }

    /**
     * Get base query builder for target.
     * Reuses legacy TargetRepository logic.
     */
    public function getTarget(bool $byDoctor = false)
    {
        if ($byDoctor) {
            return Target::query()
                ->where('targets.by_doctor', true)
                ->leftJoin('doctors', 'targets.doctor_id', '=', 'doctors.id')
                ->leftJoin('users', 'doctors.user_id', '=', 'users.id')
                ->leftJoin('profiles', function ($join) {
                    $join->on('users.id', '=', 'profiles.profileable_id')
                         ->where('profiles.profileable_type', '=', UserTenant::class);
                })
                ->select(
                    'targets.id as id',
                    'targets.start_date as start_date',
                    'targets.end_date as end_date',
                    'targets.amount as amount',
                    'targets.description as description',
                    'targets.doctor_id as doctor_id',
                    'targets.user_id as user_id',
                    'targets.hospital_id as hospital_id',
                    'targets.by_doctor as by_doctor',
                    'targets.created_at as created_at',
                    'targets.updated_at as updated_at'
                )
                ->selectRaw("COALESCE(NULLIF(CONCAT(COALESCE(profiles.firstname, ''), ' ', COALESCE(profiles.lastname, '')), ' '), users.name, 'Doctor #' || targets.doctor_id) AS fullname");
        }

        return Target::query()->where('targets.by_doctor', false);
    }

    /**
     * Legacy target amount aggregation by date.
     */
    public function getTargetAmountByDate($start = '', $end = '')
    {
        if ($start == '' && $end == '') {
            $start = $end = Carbon::now()->format('Y-m-d');
        }

        $query = Target::query()->where('by_doctor', false);
        if (!empty($start) && !empty($end)) {
            $data = $query->whereDate('start_date', '<=', $end)
                          ->whereDate('end_date', '>=', $start);
        } elseif ($start && !empty($end)) {
            $data = $query->whereDate('end_date', '>=', $start)
                          ->orWhereDate('start_date', '>=', $start);
        } elseif (!empty($start) && $end) {
            $data = $query->whereDate('start_date', '<=', $end)
                          ->orWhereDate('end_date', '>=', $end);
        } else {
            $data = $query;
        }

        return (float) $data->sum('amount');
    }

    /**
     * Calculate actual income from payments received in date range.
     * Accurately factors in partial payments, payment refunds (negative amounts), and non-canceled invoices.
     */
    public function getIncomeBy($startDate, $endDate, $doctorId = null): float
    {
        if (empty($startDate) || empty($endDate)) {
            return 0.0;
        }

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $query = DB::table('payments')
            ->join('medical_histories', 'payments.medical_history_id', '=', 'medical_histories.id')
            ->whereNull('payments.deleted_at')
            ->whereNull('medical_histories.deleted_at')
            ->where('medical_histories.status', '!=', MedicalHistory::CANCELED)
            ->whereBetween('payments.date', [$start, $end]);

        if (!empty($doctorId)) {
            $query->where('medical_histories.doctor_id', $doctorId);
        }

        $income = (float) $query->sum('payments.amount');

        return max(0.0, $income);
    }

    /**
     * Clinic Target DataTable query (by_doctor = false).
     */
    public function getClinicDataTable()
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
                $q->where(DB::raw('LOWER(COALESCE(description, \'\'))'), 'LIKE', "%{$term}%");

                if (is_numeric($value)) {
                    $q->orWhere('amount', (float) $value)
                      ->orWhere('id', (int) $value);
                }
            });
        });

        $paginated = QueryBuilder::for(Target::class)
            ->where('by_doctor', false)
            ->defaultSort('-id')
            ->allowedSorts([
                'id',
                'start_date',
                'end_date',
                'amount',
                'created_at',
            ])
            ->allowedFilters([
                $globalSearch,
            ])
            ->paginate()
            ->withQueryString();

        // Calculate income and result ONCE per row on the paginated page slice
        $paginated->through(function ($target) {
            $startDate = $target->getRawOriginal('start_date') ?: $target->start_date;
            $endDate = $target->getRawOriginal('end_date') ?: $target->end_date;

            $income = $this->getIncomeBy($startDate, $endDate, null);
            $targetAmount = (float) ($target->amount ?? 0);
            $resultPct = ($income > 0 && $targetAmount > 0) ? ($income / $targetAmount) * 100 : 0.0;

            $target->income = $income;
            $target->income_formatted = '$' . number_format($income, 2);
            $target->result = sprintf('%.2f%%', $resultPct);
            $target->result_percentage = $resultPct;

            return $target;
        });

        return $paginated;
    }

    /**
     * Doctor Target DataTable query (by_doctor = true).
     */
    public function getDoctorDataTable()
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
                $q->where(DB::raw('LOWER(COALESCE(targets.description, \'\'))'), 'LIKE', "%{$term}%")
                  ->orWhereHas('doctor.user', function ($uQuery) use ($term) {
                      $uQuery->where(DB::raw('LOWER(name)'), 'LIKE', "%{$term}%")
                             ->orWhereHas('profile', function ($pQuery) use ($term) {
                                 $pQuery->where(DB::raw('LOWER(COALESCE(firstname, \'\'))'), 'LIKE', "%{$term}%")
                                        ->orWhere(DB::raw('LOWER(COALESCE(lastname, \'\'))'), 'LIKE', "%{$term}%")
                                        ->orWhere(DB::raw("LOWER(CONCAT(COALESCE(firstname, ''), ' ', COALESCE(lastname, '')))"), 'LIKE', "%{$term}%");
                             });
                  });

                if (is_numeric($value)) {
                    $q->orWhere('targets.amount', (float) $value)
                      ->orWhere('targets.id', (int) $value)
                      ->orWhere('targets.doctor_id', (int) $value);
                }
            });
        });

        $paginated = QueryBuilder::for(Target::class)
            ->where('targets.by_doctor', true)
            ->with(['doctor.user.profile'])
            ->defaultSort('-id')
            ->allowedSorts([
                'id',
                'start_date',
                'end_date',
                'amount',
                'created_at',
            ])
            ->allowedFilters([
                $globalSearch,
            ])
            ->paginate()
            ->withQueryString();

        // Calculate doctor income and result ONCE per row on the paginated page slice
        $paginated->through(function ($target) {
            $startDate = $target->getRawOriginal('start_date') ?: $target->start_date;
            $endDate = $target->getRawOriginal('end_date') ?: $target->end_date;

            $income = $this->getIncomeBy($startDate, $endDate, $target->doctor_id);
            $targetAmount = (float) ($target->amount ?? 0);
            $resultPct = ($income > 0 && $targetAmount > 0) ? ($income / $targetAmount) * 100 : 0.0;

            $target->income = $income;
            $target->income_formatted = '$' . number_format($income, 2);
            $target->result = sprintf('%.2f%%', $resultPct);
            $target->result_percentage = $resultPct;

            return $target;
        });

        return $paginated;
    }

    /**
     * Columns for Clinic Target DataTable.
     * Exact order:
     * 1. Action
     * 2. Start Date
     * 3. End Date
     * 4. Target ($)
     * 5. Income ($)
     * 6. Result as % of Target
     * 7. Description
     */
    public function columnsClinicDataTable(InertiaTable $table)
    {
        $table->perPageOptions(config('core.per_page_option') ?? [10, 15, 30, 50, 100]);
        $table->withGlobalSearch();

        $table->column(key: 'actions', label: 'Action');
        $table->column(key: 'start_date', label: 'Start Date', sortable: true);
        $table->column(key: 'end_date', label: 'End Date', sortable: true);
        $table->column(key: 'amount', label: 'Target ($)', sortable: true);
        $table->column(key: 'income', label: 'Income ($)');
        $table->column(key: 'result', label: 'Result as % of Target');
        $table->column(key: 'description', label: 'Description');
    }

    /**
     * Columns for Doctor Target DataTable.
     * Exact order:
     * 1. Action
     * 2. Start Date
     * 3. Doctor
     * 4. End Date
     * 5. Target ($)
     * 6. Income ($)
     * 7. Result as % of Target
     * 8. Description
     */
    public function columnsDoctorDataTable(InertiaTable $table)
    {
        $table->perPageOptions(config('core.per_page_option') ?? [10, 15, 30, 50, 100]);
        $table->withGlobalSearch();

        $table->column(key: 'actions', label: 'Action');
        $table->column(key: 'start_date', label: 'Start Date', sortable: true);
        $table->column(key: 'doctor', label: 'Doctor');
        $table->column(key: 'end_date', label: 'End Date', sortable: true);
        $table->column(key: 'amount', label: 'Target ($)', sortable: true);
        $table->column(key: 'income', label: 'Income ($)');
        $table->column(key: 'result', label: 'Result as % of Target');
        $table->column(key: 'description', label: 'Description');
    }

    public function createClinicTarget(array $data): Target
    {
        return DB::transaction(function () use ($data) {
            return Target::create([
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'amount' => (float) $data['amount'],
                'description' => $data['description'] ?? null,
                'by_doctor' => false,
                'doctor_id' => null,
                'user_id' => auth()->id(),
                'hospital_id' => auth()->user()?->hospital_id ?: 1,
            ]);
        });
    }

    public function updateClinicTarget(int $id, array $data): Target
    {
        return DB::transaction(function () use ($id, $data) {
            $target = Target::where('by_doctor', false)->findOrFail($id);

            if ($target->is_verified) {
                abort(403, 'Verified targets cannot be updated.');
            }

            $target->update([
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'amount' => (float) $data['amount'],
                'description' => $data['description'] ?? null,
                'by_doctor' => false,
                'doctor_id' => null,
            ]);

            return $target;
        });
    }

    public function createDoctorTarget(array $data): Target
    {
        return DB::transaction(function () use ($data) {
            return Target::create([
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'doctor_id' => (int) $data['doctor_id'],
                'amount' => (float) $data['amount'],
                'description' => $data['description'] ?? null,
                'by_doctor' => true,
                'user_id' => auth()->id(),
                'hospital_id' => auth()->user()?->hospital_id ?: 1,
            ]);
        });
    }

    public function updateDoctorTarget(int $id, array $data): Target
    {
        return DB::transaction(function () use ($id, $data) {
            $target = Target::where('by_doctor', true)->findOrFail($id);

            if ($target->is_verified) {
                abort(403, 'Verified targets cannot be updated.');
            }

            $target->update([
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'doctor_id' => (int) $data['doctor_id'],
                'amount' => (float) $data['amount'],
                'description' => $data['description'] ?? null,
                'by_doctor' => true,
            ]);

            return $target;
        });
    }

    public function deleteTarget(int $id, bool $byDoctor): bool
    {
        return DB::transaction(function () use ($id, $byDoctor) {
            $target = Target::where('by_doctor', $byDoctor)->findOrFail($id);

            if ($target->is_verified) {
                abort(403, 'Verified targets cannot be deleted.');
            }

            return (bool) $target->delete();
        });
    }
}
