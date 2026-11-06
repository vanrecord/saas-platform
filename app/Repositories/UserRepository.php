<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;
use Illuminate\Contracts\Container\Container;
use App\Models\User;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Repositories\RoleRepository;


class UserRepository extends BaseRepository
{
	public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(\App\Models\User::class)
             ->setRepositoryId('rinvex.repository.uniqueid');

    }
    public function getDataTable()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $value = strtolower($value);
                $query->where(\DB::raw('LOWER(name)'), 'LIKE', "%{$value}%")
                    ->orWhere(\DB::raw('LOWER(email)'), 'LIKE', "%{$value}%")
                    ->orWhereHas('createdBy', function($query) use ($value) {
                        $query->where('name','ILIKE', "%{$value}%");
                    })
                    ->orWhereHas('updatedBy', function($query) use ($value) {
                        $query->where('name','ILIKE', "%{$value}%");
                    });
            });
        });

        $currentUser = \Auth::user() ?? \Auth::guard('tenant')->user();
        if(!$currentUser || !$currentUser->level_access){
            $users = $currentUser && method_exists($currentUser, 'descendantsAndSelf') ? $currentUser->descendantsAndSelf() : app(User::class)->query();
        }else {
            $users = app(User::class)->query();
        }

        $users = QueryBuilder::for($users)
            ->defaultSort('-id')
            ->allowedFields(['id', 'name', 'email', 'is_active', 'level_access', 'created_at','updated_at','createdBy.name','updatedBy.name','roles.name','email_verified_at'])
            ->allowedIncludes(['roles'])
            ->with('roles','createdBy','updatedBy')
            ->where(function($query){
                $query->where('level_access','!=',User::SUPER_ADMIN)
                ->orWhereNull('level_access');
            })
            ->allowedSorts(['id', 'name', 'email', 'level_access', 'created_at'])
            ->allowedFilters(['name', 'email', 'created_at', AllowedFilter::exact('is_active'), AllowedFilter::exact('level_access'),AllowedFilter::exact('roles.name'), $globalSearch])
            ->paginate()
            ->withQueryString();
        return $users;
    }

    public function columnsDataTable($table)
    {
        $table->perPageOptions(config('core.per_page_option'));
        $table->selectFilter(key: 'is_active', label: 'Status', options: [
            true  => 'Active',
            false => 'Inactive'
        ]);
        $table->selectFilter(key: 'level_access', label: 'Is Admin', options: [
            true  => 'Active',
            false => 'Inactive'
        ]);
        $table->withGlobalSearch();
        $table->column(key:'actions',label:'Action');
        $table->column(key:'id',label:'ID');
        $table->column(key:'profile_photo_url',label:'Avatar');
        $table->column(key:'name' ,label:'Name');
        $table->column(key:'email',label:'Email');
        $table->column(key:'level_access',label:'Admin');
        $table->column(key:'is_active',label:'Status');
        $table->column(key:'created_at',label:'Created Date');
        $table->column(key:'createdBy.name',label:'Created By');
        $table->column(key:'updated_at',label:'Modified Date');
        $table->column(key:'updatedBy.name',label:'Modified By');
        
    }
}