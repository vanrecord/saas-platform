<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\RoleRequest;
use App\Repositories\RoleRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class RoleController extends Controller
{
    protected RoleRepositoryInterface $role;

    public function __construct(RoleRepositoryInterface $role)
    {
        $this->role = $role;
    }

    public function index(): Response
    {
        abort_if(!auth()->user()?->can('role:view'), 403, 'Unauthorized to view roles.');

        $roles = $this->role->getDataTable();

        return Inertia::render('Tenant/Role/Index', [
            'roles' => $roles,
        ])->table(function (InertiaTable $table) {
            $this->role->columnsDataTable($table);
        });
    }

    public function create(): Response
    {
        abort_if(!auth()->user()?->can('role:create'), 403, 'Unauthorized to create roles.');

        $permissionGroups = $this->role->getAvailablePermissions();

        return Inertia::render('Tenant/Role/Create', [
            'permissionGroups' => $permissionGroups,
        ]);
    }

    public function store(RoleRequest $request): RedirectResponse
    {
        abort_if(!auth()->user()?->can('role:save'), 403, 'Unauthorized to save roles.');

        $this->role->createRole($request->validated());

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit($id): Response
    {
        abort_if(!auth()->user()?->can('role:edit'), 403, 'Unauthorized to edit roles.');

        $role = $this->role->findWithPermissions((int) $id);

        if (!$role) {
            abort(404, 'Role not found.');
        }

        $assignedPermissions = $role->permissions->pluck('name')->values()->all();
        $permissionGroups = $this->role->getAvailablePermissions();

        return Inertia::render('Tenant/Role/Edit', [
            'role' => $role,
            'assignedPermissions' => $assignedPermissions,
            'permissionGroups' => $permissionGroups,
        ]);
    }

    public function update(RoleRequest $request, $id): RedirectResponse
    {
        abort_if(!auth()->user()?->can('role:save'), 403, 'Unauthorized to save roles.');

        $updated = $this->role->updateRole((int) $id, $request->validated());

        if (!$updated) {
            abort(404, 'Role not found.');
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        abort_if(!auth()->user()?->can('role:delete'), 403, 'Unauthorized to delete roles.');

        $deleted = $this->role->deleteRole((int) $id);

        if (!$deleted) {
            return redirect()->back()
                ->with('error', 'Cannot delete this role.');
        }

        return redirect()->back()
            ->with('success', 'Role deleted successfully.');
    }
}
