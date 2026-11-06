<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Inertia\Inertia;
use App\Jobs\CreateRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Actions\User\CreateUser;
use App\Actions\User\UpdateUser;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Actions\Fortify\CreateNewUser;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class UserController extends Controller
{
    protected $user;
    protected $role;

    public function __construct()
    {
        $this->user = App(UserRepository::class);
        $this->role = App(RoleRepository::class);
    }

    public function index()
    {
        $this->authorize(app("request")->route()->getName(), User::class);
        $users = $this->user->getDataTable();
        $view = "User/Index";

        return Inertia::render($view, [
            'users' => $users,
        ])->table(function (InertiaTable $table)  {
            $this->user->columnsDataTable($table);
        });
    }

    private function getColumns()
    {
        return [
            'id' => 'ID',
            'profile_photo_url' => 'Avatar',
            'name'     => 'Name',
            'email' => 'Email',
            'level_access' => 'Admin',
            'is_active' => "Status",
            'created_at' => "Created At"
        ];
    }

    public function create()
    {
        $this->authorize(app("request")->route()->getName(), User::class);
        $view = "User/Create";
        return Inertia::render($view);
    }

    public function store(CreateUser $creator, Request $request)
    {
        $this->authorize(app("request")->route()->getName(), User::class);
        $user = $creator->create($request->all());
        return redirect()->route('user.index');
    }

    public function edit($id)
    {
        $this->authorize(app("request")->route()->getName(), User::class);
        $view = "User/Edit";
        
        $user = $this->user->find($id);

        return Inertia::render($view, [
            'user' => $user
        ]);
    }

    public function update($id, UpdateUser $updator, Request $request)
    {
        $this->authorize('user.store', User::class);

        $user =  $this->user->find($id);
        $updator->update($user, $request->all());
        return redirect()->route('user.index');
    }

    public function destroy($id)
    {
        $this->authorize(app("request")->route()->getName(), User::class);
        $user =  $this->user->find($id);
        $this->user->update($user, ['is_active' => false]);
        return redirect()->route('user.index');
    }

    public function emailVerified($id,UpdateUser $updator, Request $request)
    {
        $attachPerms = $this->attachRole();
        $role = app(Role::class)->where('name', $attachPerms['name'])->where('guard_name', 'web')->first();
        $user = $this->user->find($id);
        if ($role == null) {
            CreateRole::dispatchSync($attachPerms);
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        }
        $role_old = $user->roles->pluck('name')->toArray();
        $role_old[] = $attachPerms['name'];

        $request['name'] = $user->name;
        $request['email'] = $user->email;
        $request['is_active'] = $user->is_active;
        $request['level_access'] = $user->level_access;
        $request['role'] = $role_old;
        $request['email_verified_at'] = $request->email_verified_at;
        $updator->update($user, $request->all());
        return redirect()->back();
    }
    public function attachRole()
    {
        $attach = [
            'name' =>'owner',
            'perms' =>[
                0 => "dashboard.index",
                1 => "domain.index",
                2 => "domain.create",
                3 => "domain.edit",
                4 => "domain.store",
            ],
            'id'=>null
        ];
        return $attach;
    }

    public function resetPassword($id)
    {
        $user =  $this->user->find($id);
        $reset_password = "12345678";
        $user->update([
           'password' => Hash::make($reset_password),
        ]);
        return redirect()->route('user.index');
    }
}
