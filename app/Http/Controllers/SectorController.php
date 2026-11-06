<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Sector;
use App\Models\User;
use App\Actions\Sector\CreateSector;
use App\Actions\Sector\UpdateSector;
use App\Http\Controllers\Controller;
use App\Repositories\SectorRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class SectorController extends Controller
{
    public function __construct()
    {
        $this->sector = App(SectorRepository::class);
        $this->user = App(UserRepository::class);
    }

    public function index()
    {
        $this->authorize(app("request")->route()->getName(), Sector::class);
        $sectors = $this->sector->getDataTable();
        $view = "Sector/Index";
        return Inertia::render($view,[
            'sectors' => $sectors,
        ])->table(function (InertiaTable $table){
            $this->sector->columnsDataTable($table);
        });
    }
    private function getColumns()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'name_native' => 'Name_Native',
            'name_international' => 'Name_International',
            'is_active' => "Status",
            'created_at' => "Created Date",
            'createdBy' => "Created By",
            'updated_at' => "Modified Date",
            'updatedBy' => "Modified By"
        ];
    }
    public function create()
    {
        $this->authorize(app("request")->route()->getName(), Sector::class);
        $view = "Sector/Create";
        return Inertia::render($view);
    }
    public function store(CreateSector $creator, Request $request)
    {
        $this->authorize(app("request")->route()->getName(), Sector::class);
        $creator->create($request->all());
        return redirect()->route('sector.index');

    }

    public function edit($id){
        $this->authorize(app("request")->route()->getName(), Sector::class);
        $view = "Sector/Edit";
        $sector = $this->sector->find($id);
        return Inertia::render($view,[
            'sector' => $sector
        ]);
    }

    public function update($id, UpdateSector $updator, Request $request)
    {
        $this->authorize('sector.store', Sector::class);
        $sector = $this->sector->find($id);
        $updator->update($sector,$request->all());
        return redirect()->route('sector.index');

    }

    public function destroy($id)
    {
        $this->authorize(app("request")->route()->getName(), Sector::class);
        $sector = $this->sector->find($id);
        if(!empty($sector->domains->toArray())){
            return redirect()->route('sector.index')->withErrors([
              'error' => 'This record has use in domain'
            ]);
        }
        $sector->delete();
        return redirect()->route('sector.index');
    }


}
