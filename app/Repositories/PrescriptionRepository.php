<?php

namespace App\Repositories;

use App\Models\Tenant\Prescription;
use App\Repositories\Base\BaseRepository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PrescriptionRepository extends BaseRepository implements PrescriptionRepositoryInterface
{
    public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(Prescription::class)
             ->setRepositoryId('rinvex.repository.prescription');
    }

    public function getDataTable()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            $term = strtolower(trim((string) $value));
            $query->where(function ($query) use ($term, $value) {
                $query->where(DB::raw('LOWER(COALESCE(prescription, \'\'))'), 'LIKE', "%{$term}%")
                      ->orWhereHas('patient', function ($q) use ($term) {
                          $q->where(DB::raw("LOWER(CONCAT(COALESCE(firstname, ''), ' ', COALESCE(lastname, '')))"), 'LIKE', "%{$term}%")
                            ->orWhere('id_number', 'LIKE', "%{$term}%");
                      })
                      ->orWhereHas('prescriptionDetails.medicine', function ($q) use ($term) {
                          $q->where(DB::raw('LOWER(name)'), 'LIKE', "%{$term}%");
                      });

                if (is_numeric($value)) {
                    $query->orWhere('id', (int) $value);
                }
            });
        });

        return QueryBuilder::for(Prescription::class)
            ->with(['patient', 'prescriptionDetails.medicine', 'medicalHistory', 'user'])
            ->defaultSort('-id')
            ->allowedSorts([
                'id',
                'created_at',
            ])
            ->allowedFilters([
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
        $table->column(key: 'id', label: 'ID', sortable: true);
        $table->column(key: 'patient', label: 'Patient');
        $table->column(key: 'prescription', label: 'Prescription');
        $table->column(key: 'created_at', label: 'Date', sortable: true);
    }
}
