<?php
namespace App\Repositories;

use App\Repositories\Base\BaseRepository;
use Illuminate\Contracts\Container\Container;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Models\Domain;

class DomainRepository extends BaseRepository
{

	public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(\App\Models\Domain::class)
			 ->setRepositoryId('rinvex.repository.uniqueid');

    }

    public function getDataTable($request="")
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            if(is_array($value)) $value = implode(",", $value);
            $query->where(function ($query) use ($value) {
                $query->where('company', 'ILIKE', "%{$value}%")
                      ->orwhere('domain','ILIKE', "%{$value}%");
                $query->orWhereHas('parent', function ($query) use ($value) {
                    $query->where('company', 'ILIKE', "%{$value}%");
                })
                ->orWhereHas('createdBy', function($query) use ($value) {
                    $query->where('name','ILIKE', "%{$value}%");
                })
                ->orWhereHas('updatedBy', function($query) use ($value) {
                    $query->where('name','ILIKE', "%{$value}%");
                });
            });
        });

        $domain = app(Domain::class)->query();

        if(!empty($request) && count($request) && !empty($request['filter']) && !empty($request['filter']['filter_domain'])){
            $filter_sub_domain_n_self = $request['filter']['filter_domain'];
            $domain = app(Domain::class)->query()->where('tenant_id',$filter_sub_domain_n_self)->first()->descendantsAndSelf();
        }


        $domains = QueryBuilder::for($domain->FilterUser())
            ->with('tenant','parent','createdBy','updatedBy')
            ->allowedIncludes(['tenant','createdBy','updatedBy'])
            ->allowedSorts(['domain','company','tenant_id','is_active','created_at','updated_at'])
            ->allowedFilters(['domain','company',AllowedFilter::exact('id'),'tenant_id','createdBy.name','updatedBy.name',
                AllowedFilter::callback('filter_domain',function($query){}),$globalSearch, 'parent.company'])
            ->paginate()
            ->withQueryString();
        return $domains;
    }

    public function columnsDataTable($table,$list_domain)
    {
        $table->perPageOptions(config('core.per_page_option'));
        $table->selectFilter(key:'filter_domain',label:'Descendant domain',options:$list_domain);
        $table->withGlobalSearch();
        $table->column(key:'actions',label:'Action');
        $table->column(key:'company',label:'Tenant Name',searchable: true,sortable:true);
        $table->column(key:'tenant_id',label:'Tenant Identifier',sortable:true);
        $table->column(key:'domain' ,label:'Domain',sortable:true);
        $table->column(key:'database',label:'Database');
        $table->column(key:'is_active',label:'Status');
        $table->column(key:'created_at' ,label:'Created Date',sortable: true);
        $table->column(key:'parent.company',label:'Parent',searchable: true);
    }
}