<?php
namespace App\Http\Controllers;

use App\Models\Role;
use Inertia\Inertia;
use App\Jobs\CreateRole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\RoleRepository;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class RoleController extends Controller
{
    public function __construct(RoleRepository $role)
    {
       $this->role = $role ;
    }

    public function index()
    {
        $this->authorize(app("request")->route()->getName(),Role::class);
        $roles = $this->role->getDataTable();
        return Inertia::render('Roles/Index', [
            'roles' => $roles
        ])->table(function (InertiaTable $table) {
            $this->role->columnsDataTable($table);
        });
    }

    private function getColumns(){
        return [
            'id' => 'ID',
            'name'     => 'Name',
            'created_at' =>"Created Date",
            'updated_at' => 'Modified Date'
        ];
    }

    public function create()
    {
        $this->authorize(app("request")->route()->getName(),Role::class);

        $perms = config('fwta.permission');
        return Inertia::render('Roles/Create', [
            'permissions' => $perms['core']['basic']
        ]);
    }

    public function store(Request $request )
    {
        $this->authorize(app("request")->route()->getName(),Role::class);

        $request->validate([
            'name' => 'required|string|unique:roles|max:255',
        ]);

        CreateRole::dispatch($request->merge(['id'=>null])->all()); // queue work background process
        return redirect()->route('role.index');
    }

    public function edit($id)
    {
        $this->authorize(app("request")->route()->getName(),Role::class);

        $role = $this->role->find($id);
        $perms = config('fwta.permission');
        return Inertia::render('Roles/Edit',[
            'permissions' => $perms['core']['basic'],
            'role' => $role,
            'rolePerms' => Inertia::lazy(fn () => $role->permissions->pluck('name')) ,

        ]);
    }

    public function update($id,Request $request )
    {
        $this->authorize('role.store',Role::class);

        $request->validate([
            'name' => 'required|string||max:255|unique:roles,name,'.$id,
        ]);
        CreateRole::dispatch($request->merge(['id'=>$id])->all()); // queue work background process
        return redirect()->route('role.index');
    }

    public function destroy($id){
        $this->authorize(app("request")->route()->getName(),Role::class);

        $this->role->delete($id);
        return redirect()->route('role.index');
    }
}