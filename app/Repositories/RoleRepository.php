<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;
use Illuminate\Contracts\Container\Container;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Models\Role;


class RoleRepository extends BaseRepository
{
	public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(\App\Models\Role::class)
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
        $authUser = auth()->user() ?? auth('tenant')->user();
        if($authUser && (method_exists($authUser, 'isAdmin') && $authUser->isAdmin() || method_exists($authUser, 'isSuperAdmin') && $authUser->isSuperAdmin() || (int)$authUser->getRawOriginal('level_access') === 1)){
           $roles = QueryBuilder::for(app(Role::class)->query())
            ->defaultSort('id')
            ->with('createdBy','updatedBy');
        }else{
            $roles = QueryBuilder::for(app(Role::class)->query())
            ->defaultSort('id')
            ->with('createdBy','updatedBy')
            ->where('created_by', $authUser?->id ?? 0)
            ->orWhere('name',Role::IS_OWNER);
        }
            $roles = $roles->allowedFields(['id', 'name', 'created_at', 'updated_at'])
            ->allowedSorts(['id', 'name','created_at', 'updated_at'])
            ->allowedFilters(['name','createdBy.name','updatedBy.name', $globalSearch])
            ->paginate()
            ->withQueryString();
        return $roles;
    }
    public function columnsDataTable($table)
    {
        $table->perPageOptions(config('core.per_page_option'));
        $table->withGlobalSearch();
        $table->column(key:'actions',label:'Action');
        $table->column(key:'id',label:'ID');
        $table->column(key:'name',label:'Name',searchable: true,sortable:true);
        $table->column(key:'created_at' ,label:'Created Date');
        $table->column(key:'updated_at',label:'Modified Date',sortable:true);
        $table->column(key:'createdBy.name',label:'Created By',searchable: true);
        $table->column(key:'updatedBy.name',label:'Modified By',searchable: true);
    }

    public function getRoleByUser($id)
    {
        return app(Role::class)->where('created_by',$id)
                               ->orWhere('name',Role::IS_OWNER)->select('id','name')->get();
    }
}