<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Actions\Type\StoreType;
use App\Repositories\PackageRepository;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class PackageController extends Controller
{
    
    protected $type;
    protected $creator;

    public function __construct()
    {
        $this->type = App(PackageRepository::class);
        $this->creator = App(StoreType::class);
    }

    public function index()
    {
        $this->authorize(app("request")->route()->getName(),Role::class);
        return Inertia::render('Type/Index',[
            'types' => fn () => $this->type->getDataTable()
        ])->table(function (InertiaTable $table){
            $this->type->columnsDataTable($table);
        });
    }

    public function create()
    {
        $this->authorize(app("request")->route()->getName(),Role::class);
        return Inertia::render('Type/Create', [
            'config_modules' => config('module')
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(app("request")->route()->getName(),Role::class);
        $this->creator->store($request->all());
        return redirect()->route('package.index');
        
    }

    public function edit($id)
    {
        $this->authorize(app("request")->route()->getName(),Role::class);
        $type = $this->type->find($id);
        return Inertia::render('Type/Edit', [
            'type' => fn () => $type,
            'config_modules' => config('module'),
            'typeModules' => fn () => $type->modules->pluck('name')
        ]);
    }

    public function destroy($id)
    {
        $this->authorize(app("request")->route()->getName(),Role::class);
        $this->type->delete($id)->modules()->detach();
        return redirect()->route('package.index');
    }
}
