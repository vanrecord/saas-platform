<?php

namespace App\Repositories;

use App\Models\Tenant\Medicine;
use App\Repositories\Base\BaseRepository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class MedicineRepository extends BaseRepository implements MedicineRepositoryInterface
{
    public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(Medicine::class)
             ->setRepositoryId('rinvex.repository.medicine');
    }

    public function getDataTable()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            $term = strtolower(trim((string) $value));
            $query->where(function ($query) use ($term, $value) {
                $query->where(DB::raw('LOWER(name)'), 'LIKE', "%{$term}%")
                      ->orWhere(DB::raw('LOWER(COALESCE(description, \'\'))'), 'LIKE', "%{$term}%");

                if (is_numeric($value)) {
                    $query->orWhere('id', (int) $value);
                }
            });
        });

        return QueryBuilder::for(Medicine::class)
            ->defaultSort('-id')
            ->allowedFields([
                'id',
                'name',
                'description',
                'created_at',
                'updated_at',
            ])
            ->allowedSorts([
                'id',
                'name',
                'created_at',
                'updated_at',
            ])
            ->allowedFilters([
                'name',
                'description',
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
        $table->column(key: 'name', label: 'Medicine Name', sortable: true, searchable: true);
        $table->column(key: 'description', label: 'Description', searchable: true);
        $table->column(key: 'created_at', label: 'Created Date', sortable: true);
    }
}
