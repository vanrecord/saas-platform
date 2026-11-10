<?php

namespace App\Repositories;

use App\Models\Tenant\Patient;
use App\Repositories\Base\BaseRepository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PatientRepository extends BaseRepository implements PatientRepositoryInterface
{
    public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(Patient::class)
             ->setRepositoryId('rinvex.repository.uniqueid');
    }

    public function getDataTable()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            $query->where(function ($query) use ($value) {
                $term = strtolower(trim($value));
                $query->where(DB::raw('LOWER(firstname)'), 'LIKE', "%{$term}%")
                    ->orWhere(DB::raw('LOWER(lastname)'), 'LIKE', "%{$term}%")
                    ->orWhere(DB::raw("LOWER(CONCAT(COALESCE(firstname, ''), ' ', COALESCE(lastname, '')))"), 'LIKE', "%{$term}%")
                    ->orWhere(DB::raw('LOWER(phone)'), 'LIKE', "%{$term}%")
                    ->orWhere(DB::raw('LOWER(email)'), 'LIKE', "%{$term}%")
                    ->orWhere(DB::raw('LOWER(card_code)'), 'LIKE', "%{$term}%");

                if (is_numeric($value)) {
                    $query->orWhere('id_number', (int) $value)
                          ->orWhere('id', (int) $value);
                }
            });
        });

        $patients = QueryBuilder::for(Patient::class)
            ->defaultSort('-id')
            ->allowedFields([
                'id',
                'id_number',
                'firstname',
                'lastname',
                'gender',
                'age',
                'dob',
                'phone',
                'email',
                'profession',
                'address',
                'balance',
                'avatar',
                'card_activated',
                'card_code',
                'is_trail_card',
                'created_at',
                'updated_at',
            ])
            ->allowedSorts([
                'id',
                'id_number',
                'firstname',
                'lastname',
                'gender',
                'age',
                'dob',
                'phone',
                'email',
                'profession',
                'created_at',
            ])
            ->allowedFilters([
                'firstname',
                'lastname',
                'phone',
                'email',
                AllowedFilter::callback('gender', function ($query, $value) {
                    if ($value === 'M' || $value === 'male') {
                        $query->whereIn('gender', ['M', 'male', 'Male']);
                    } elseif ($value === 'F' || $value === 'female') {
                        $query->whereIn('gender', ['F', 'female', 'Female']);
                    } else {
                        $query->where('gender', $value);
                    }
                }),
                AllowedFilter::exact('id_number'),
                $globalSearch,
            ])
            ->paginate()
            ->withQueryString();

        return $patients;
    }

    public function columnsDataTable($table)
    {
        $table->perPageOptions(config('core.per_page_option') ?? [10, 15, 30, 50, 100]);
        $table->selectFilter(key: 'gender', label: 'Gender', options: [
            'M' => 'Male',
            'F' => 'Female',
        ]);
        $table->withGlobalSearch();
        $table->column(key: 'actions', label: 'Action');
        $table->column(key: 'id', label: 'ID', sortable: true);
        $table->column(key: 'avatar', label: 'Avatar');
        $table->column(key: 'id_number', label: 'ID Patient', sortable: true, searchable: true);
        $table->column(key: 'firstname', label: 'First Name', sortable: true, searchable: true);
        $table->column(key: 'lastname', label: 'Last Name', sortable: true, searchable: true);
        $table->column(key: 'gender', label: 'Gender', sortable: true);
        $table->column(key: 'age', label: 'Age', sortable: true);
        $table->column(key: 'dob', label: 'Date of Birth', sortable: true);
        $table->column(key: 'phone', label: 'Phone', sortable: true, searchable: true);
        $table->column(key: 'email', label: 'Email', sortable: true, searchable: true);
        $table->column(key: 'created_at', label: 'Created Date', sortable: true);
    }

    public function getNextIdNumber(): int
    {
        return Patient::getNextIdNumber();
    }

    public function findWithDetails($id)
    {
        $patient = $this->find($id);
        if ($patient) {
            $patient->load([
                'dentalCharts' => fn ($q) => $q->withCount('teeth')->latest('chart_date'),
                'visits' => fn ($q) => $q->latest(),
            ]);
        }
        return $patient;
    }
}
