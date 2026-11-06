<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;
use Illuminate\Contracts\Container\Container;
use App\Models\Village;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class VillageRepository extends BaseRepository
{
	public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(Village::class)
             ->setRepositoryId('rinvex.repository.uniqueid');

    }
    public function getDataTable()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $value = strtolower($value);
                $query->where('name_en', 'ILIKE', "%{$value}%")
                ->orWhere('name_kh', 'ILIKE', "%{$value}%")
                ->orWhere('code', 'ILIKE', "%{$value}%")
                ->orWhereHas('commune', function($query) use ($value) {
                        $query->where('name_en','ILIKE', "%{$value}%");
                    })
                ->orWhereHas('commune.district', function($query) use ($value) {
                        $query->where('name_en','ILIKE', "%{$value}%");
                    })
                ->orWhereHas('commune.district.province', function($query) use ($value) {
                        $query->where('name_en','ILIKE', "%{$value}%");
                    });
            });
        });
        $village = QueryBuilder::for(app(Village::class)->query())
            ->defaultSort('id')
            ->with('commune','commune.district','commune.district.province')
            ->allowedFields(['id','code','name_kh','name_en','type','district.name_en'])
            ->allowedIncludes(['commune','commune.district','commune.district.province'])
            ->allowedFilters(['name_en','name_kh','commune.name_en','commune.district.name_en','commune.district.province.name_en','type', $globalSearch])
            ->paginate()
            ->withQueryString();
        return $village;
    }

    public function columnsDataTable($table)
    {
        $table->perPageOptions(config('core.per_page_option'));
        $table->withGlobalSearch();
        $table->column(key:'id',label:'ID');
        $table->column(key:'code',label:'Code');
        $table->column(key:'name_kh',label:'Name Khmer',searchable: true);
        $table->column(key:'name_en',label:'Name English',searchable: true);
        $table->column(key:'type',label:'Type',searchable:true);
        $table->column(key:'commune.name_en',label:'Commune Name',searchable:true);
        $table->column(key:'commune.district.name_en',label:'District Name',searchable:true);
        $table->column(key:'commune.district.province.name_en',label:'Province Name',searchable:true);

        return $table;
    }
}