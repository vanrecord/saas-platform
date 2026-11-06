<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;
use Illuminate\Contracts\Container\Container;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Sector;
use Illuminate\Support\Facades\Auth;
// use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;


class SectorRepository extends BaseRepository
{
    public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(\App\Models\Sector::class)
             ->setRepositoryId('rinvex.repository.uniqueid');

    }

    public function getDataTable()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $value = strtolower($value);
                $query->where(\DB::raw('LOWER(name_native)'), 'LIKE', "%{$value}%")
                    ->orWhere(\DB::raw('LOWER(code)'), 'LIKE', "%{$value}%")
                    ->orWhereHas('createdBy', function($query) use ($value) {
                        $query->where('name','ILIKE', "%{$value}%");
                    })
                    ->orWhereHas('createdBy', function($query) use ($value) {
                        $query->where('name','ILIKE', "%{$value}%");
                    });
            });
        });
        $sectors = QueryBuilder::for(app(Sector::class)->query())
            ->defaultSort('-id')
            ->with('createdBy','updatedBy')
            ->withCount('domains')
            ->allowedFields(['id','code', 'name_native', 'name_international', 'is_active','created_at','updated_at','createdBy.name','updatedBy.name'])
            ->allowedIncludes(['createdBy','updatedBy'])
            ->allowedSorts(['id','code' ,'name_native', 'name_international', 'is_active','created_at','updated_at'])
            ->allowedFilters(['code','name_native', 'name_international','createdBy.name','updatedBy.name',
                            AllowedFilter::exact('is_active'),
                            AllowedFilter::exact('created_at'),
                            AllowedFilter::exact('updated_at'), $globalSearch])
            ->paginate()
            ->withQueryString();

        return $sectors;
    }

    public function columnsDataTable($table)
    {
            $table->perPageOptions(config('core.per_page_option'));
            $table->selectFilter(key: 'is_active', label: 'Status', options: [
                true  => 'Active',
                false => 'Inactive'
            ]);
            $table->withGlobalSearch();
            $table->column(key:'actions',label:'Action');
            $table->column(key:'id',label:'ID');
            $table->column(key:'code',label:'Code',searchable: true,sortable:true);
            $table->column(key:'name_native',label:'Name Native',searchable: true,sortable:true);
            $table->column(key:'name_international' ,label:'Name International',searchable: true,sortable:true);
            $table->column(key:'is_active',label:'Status');
            $table->column(key:'created_at',label:'Created Date',sortable:true);
            $table->column(key:'createdBy.name',label:'Created By',searchable: true);
            $table->column(key:'updated_at',label:'Modified Date',sortable:true);
            $table->column(key:'updatedBy.name',label:'Modified By',searchable: true);
            

        return $table;
    }
}
