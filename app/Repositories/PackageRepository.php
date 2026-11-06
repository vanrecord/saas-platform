<?php

namespace App\Repositories;

use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Repositories\Base\BaseRepository;
use Illuminate\Contracts\Container\Container;


class PackageRepository extends BaseRepository
{
	public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(\App\Models\Package::class)
             ->setRepositoryId('rinvex.repository.uniqueid');

    }

    public function getDataTable()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $value = strtolower($value);
                $query->where(\DB::raw('LOWER(name)'), 'LIKE', "%{$value}%")
                    ->orWhereHas('createdBy', function($query) use ($value) {
                        $query->where('name','ILIKE', "%{$value}%");
                    })
                    ->orWhereHas('updatedBy', function($query) use ($value) {
                        $query->where('name','ILIKE', "%{$value}%");
                    });
            });
        });
        $query = QueryBuilder::for($this->query())
            ->defaultSort('-id')
            ->with([
                'createdBy:id,name',
                'updatedBy:id,name'
            ])
            ->withCount('domains')
            ->allowedSorts(['id','code' ,'name', 'is_active',
                                AllowedSort::field('createdBy.name', 'created_by'),
                                AllowedSort::field('updatedBy.name', 'updated_by'),
                                'created_at',
                                'updated_at'
                            ])
            ->allowedFilters(['code',
                                'name',
                                'createdBy.name',
                                'updatedBy.name',
                                AllowedFilter::exact('is_active'),
                                AllowedFilter::exact('created_at'),
                                AllowedFilter::exact('updated_at'), 
                                $globalSearch
                            ])
            ->paginate()
            ->withQueryString();

        return $query;
    }

    public function columnsDataTable($table)
    {
        $table->selectFilter(key: 'is_active', label: 'Status', options: [
            true  => 'Active',
            false => 'Inactive'
        ]);
        $table->withGlobalSearch();
        $table->perPageOptions(config('core.per_page_option'));
        $table->column(key:'actions',label:'Action');
        $table->column(key:'id',label:'ID');
        $table->column(key:'name',label:'Name',searchable: true,sortable:true);
        $table->column(key:'is_active',label:'Status',sortable:true);
        $table->column(key:'created_at',label:'Created Date',sortable:true);
        $table->column(key:'createdBy.name',label:'Created By',sortable:true,searchable: true);
        $table->column(key:'updated_at',label:'Modified Date',sortable:true);
        $table->column(key:'updatedBy.name',label:'Updated By',sortable:true,searchable: true);
        

        return $table;
    }
}