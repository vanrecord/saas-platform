<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;
use Illuminate\Contracts\Container\Container;
use App\Models\Province;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProvinceRepository extends BaseRepository
{
	public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(Province::class)
             ->setRepositoryId('rinvex.repository.uniqueid');

    }
    public function getDataTable()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $value = strtolower($value);
                $query->where('name_en', 'ILIKE', "%{$value}%");
                $query->orWhere('name_kh', 'ILIKE', "%{$value}%");
            });
        });
        $province = QueryBuilder::for(app(Province::class)->query())
            ->defaultSort('id')
            ->allowedFields(['id','name_kh','name_en','province_type_kh','province_type_en'])
            ->allowedFilters(['name_en','name_kh', $globalSearch])
            ->paginate()
            ->withQueryString();

        return $province;
    }

    public function columnsDataTable($table)
    {
        $table->perPageOptions(config('core.per_page_option'));
        $table->withGlobalSearch();
        $table->column(key:'id',label:'ID');
        $table->column(key:'name_kh',label:'Name Khmer',searchable: true);
        $table->column(key:'name_en',label:'Name English',searchable: true);
        $table->column(key:'province_type_kh',label:'Province type Khmer');
        $table->column(key:'province_type_en',label:'Province type English');


        return $table;
    }
}