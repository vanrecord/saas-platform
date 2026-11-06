<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Models\Activity;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class UserLogController extends Controller
{
    public function index()
    {
        $this->authorize(app("request")->route()->getName(),Role::class);
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $value = strtolower($value);
                $query->whereHas('causer', function ($query) use ($value) {
                    $query->where(\DB::raw('LOWER(name)'), 'LIKE', "%{$value}%");
                })
                ->orWhere('description', 'ILIKE', "%{$value}%");
            });
        });

        $name = AllowedFilter::callback('causer_id', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $value = strtolower($value);
                $query->whereHas('causer', function ($query) use ($value) {
                    $query->where(\DB::raw('LOWER(name)'), 'LIKE', "%{$value}%");
                });
            });
        });
        $activityLogs = QueryBuilder::for(Activity::class)
            ->defaultSort('-id')
            ->with(['causer:id,name'])
            ->allowedFields(['subject_type','description','causer_id', 'created_at'])
            ->allowedIncludes(['causer'])
            ->allowedSorts(['subject_type','description','causer_id', 'created_at'])
            ->allowedFilters(['description',$globalSearch,$name])
            ->paginate()
            ->withQueryString();
        return Inertia::render('UserLog/Index',[
			'userLogs' => $activityLogs
		])->table(function (InertiaTable $table) {
            $table->perPageOptions(config('core.per_page_option'));
            $table->withGlobalSearch();
            $table->column(key:'id',label:'ID');
            $table->column(key:'description',label:'Action',searchable: true,sortable:true);
            $table->column(key:'causer_id' ,label:'User Name',searchable: true,sortable:true);
            $table->column(key:'subject_type',label:'Title',sortable:true);
            $table->column(key:'new_data',label:'New Data');
            $table->column(key:'old_data',label:'Old Data');
            $table->column(key:'created_at',label:'Action Date', sortable:true);
        });
    }
}
