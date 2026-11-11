<?php

namespace App\Repositories\Tenant;

use App\Models\Tenant\Role;
use App\Repositories\Base\BaseRepository;
use App\Repositories\RoleRepositoryInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\DB;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\Permission\PermissionRegistrar;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(Role::class)
             ->setRepositoryId('tenant.role.repository');
    }

    public function getDataTable()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            $term = strtolower(trim((string) $value));
            $query->where(function ($query) use ($term, $value) {
                $query->where(DB::raw('LOWER(name)'), 'LIKE', "%{$term}%");
                if (is_numeric($value)) {
                    $query->orWhere('id', (int) $value);
                }
            });
        });

        $roles = QueryBuilder::for(Role::query()->where('guard_name', 'tenant'))
            ->defaultSort('id')
            ->allowedFields(['id', 'name', 'guard_name', 'created_at', 'updated_at'])
            ->allowedSorts(['id', 'name', 'created_at', 'updated_at'])
            ->allowedFilters([
                'name',
                $globalSearch,
            ])
            ->paginate()
            ->withQueryString();

        return $roles;
    }

    public function columnsDataTable(InertiaTable $table)
    {
        $table->perPageOptions(config('core.per_page_option') ?? [10, 15, 30, 50, 100]);
        $table->withGlobalSearch();

        // Exact column order: ID, Role Name, Actions
        $table->column(key: 'id', label: 'ID', sortable: true);
        $table->column(key: 'name', label: 'Role Name', sortable: true, searchable: true);
        $table->column(key: 'actions', label: 'Actions');
    }

    public function findWithPermissions(int $id): ?Role
    {
        return Role::with('permissions')
            ->where('guard_name', 'tenant')
            ->where('id', $id)
            ->first();
    }

    public function createRole(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => 'tenant',
            ]);

            if (!empty($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return $role;
        });
    }

    public function updateRole(int $id, array $data): ?Role
    {
        return DB::transaction(function () use ($id, $data) {
            $role = $this->findWithPermissions($id);
            if (!$role) {
                return null;
            }

            $role->update([
                'name' => $data['name'],
            ]);

            $permissions = $data['permissions'] ?? [];
            $role->syncPermissions($permissions);

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return $role;
        });
    }

    public function deleteRole(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $role = $this->findWithPermissions($id);
            if (!$role || $role->isProtected()) {
                return false;
            }

            $role->syncPermissions([]);
            $deleted = (bool) $role->delete();

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return $deleted;
        });
    }

    public function getAvailablePermissions(): array
    {
        return [
            'dashboard' => [
                'id' => 'dashboard',
                'title' => 'Dashboard',
                'type' => 'single',
                'permissions' => [
                    ['key' => 'dashboard:view', 'action' => 'view', 'label' => 'View'],
                ],
            ],
            'register' => [
                'id' => 'register',
                'title' => 'Register',
                'type' => 'single',
                'permissions' => [
                    ['key' => 'register:view', 'action' => 'view', 'label' => 'View'],
                ],
            ],
            'hospital' => [
                'id' => 'hospital',
                'title' => 'Hospital',
                'type' => 'group',
                'menus' => [
                    [
                        'id' => 'patient',
                        'title' => 'Patient',
                        'permissions' => [
                            ['key' => 'patient:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'patient:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'patient:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'patient:save', 'action' => 'save', 'label' => 'Save'],
                            ['key' => 'patient:delete', 'action' => 'delete', 'label' => 'Delete'],
                        ],
                    ],
                    [
                        'id' => 'service',
                        'title' => 'Service',
                        'permissions' => [
                            ['key' => 'service:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'service:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'service:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'service:save', 'action' => 'save', 'label' => 'Save'],
                            ['key' => 'service:delete', 'action' => 'delete', 'label' => 'Delete'],
                        ],
                    ],
                    [
                        'id' => 'product',
                        'title' => 'Product',
                        'permissions' => [
                            ['key' => 'product:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'product:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'product:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'product:save', 'action' => 'save', 'label' => 'Save'],
                            ['key' => 'product:delete', 'action' => 'delete', 'label' => 'Delete'],
                        ],
                    ],
                    [
                        'id' => 'service-category',
                        'title' => 'Service Category',
                        'permissions' => [
                            ['key' => 'service-category:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'service-category:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'service-category:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'service-category:save', 'action' => 'save', 'label' => 'Save'],
                            ['key' => 'service-category:delete', 'action' => 'delete', 'label' => 'Delete'],
                        ],
                    ],
                    [
                        'id' => 'product-category',
                        'title' => 'Product Category',
                        'permissions' => [
                            ['key' => 'product-category:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'product-category:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'product-category:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'product-category:save', 'action' => 'save', 'label' => 'Save'],
                            ['key' => 'product-category:delete', 'action' => 'delete', 'label' => 'Delete'],
                        ],
                    ],
                ],
            ],
            'administration' => [
                'id' => 'administration',
                'title' => 'Administration',
                'type' => 'group',
                'menus' => [
                    [
                        'id' => 'role',
                        'title' => 'Roles & Permissions',
                        'permissions' => [
                            ['key' => 'role:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'role:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'role:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'role:save', 'action' => 'save', 'label' => 'Save'],
                            ['key' => 'role:delete', 'action' => 'delete', 'label' => 'Delete'],
                        ],
                    ],
                    [
                        'id' => 'doctor',
                        'title' => 'Doctors',
                        'permissions' => [
                            ['key' => 'doctor:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'doctor:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'doctor:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'doctor:save', 'action' => 'save', 'label' => 'Save'],
                            ['key' => 'doctor:delete', 'action' => 'delete', 'label' => 'Delete'],
                        ],
                    ],
                    [
                        'id' => 'user',
                        'title' => 'Users',
                        'permissions' => [
                            ['key' => 'user:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'user:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'user:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'user:save', 'action' => 'save', 'label' => 'Save'],
                            ['key' => 'user:delete', 'action' => 'delete', 'label' => 'Delete'],
                        ],
                    ],
                ],
            ],
            'settings' => [
                'id' => 'settings',
                'title' => 'Settings',
                'type' => 'group',
                'menus' => [
                    [
                        'id' => 'payment-method',
                        'title' => 'Payment Method',
                        'permissions' => [
                            ['key' => 'payment-method:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'payment-method:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'payment-method:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'payment-method:save', 'action' => 'save', 'label' => 'Save'],
                            ['key' => 'payment-method:delete', 'action' => 'delete', 'label' => 'Delete'],
                        ],
                    ],
                ],
            ],
        ];
    }
}
