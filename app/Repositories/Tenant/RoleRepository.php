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
                foreach ($data['permissions'] as $perm) {
                    \Spatie\Permission\Models\Permission::firstOrCreate([
                        'name' => $perm,
                        'guard_name' => 'tenant',
                    ]);
                }
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
            foreach ($permissions as $perm) {
                \Spatie\Permission\Models\Permission::firstOrCreate([
                    'name' => $perm,
                    'guard_name' => 'tenant',
                ]);
            }
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
            'payment' => [
                'id' => 'payment',
                'title' => 'Payment',
                'type' => 'single',
                'permissions' => [
                    ['key' => 'payment:view', 'action' => 'view', 'label' => 'View'],
                ],
            ],
            'conditions' => [
                'id' => 'conditions',
                'title' => 'Conditions',
                'type' => 'group',
                'menus' => [
                    [
                        'id' => 'dental-condition',
                        'title' => 'Diagnostic Legend',
                        'permissions' => [
                            ['key' => 'dental-condition:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'dental-condition:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'dental-condition:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'dental-condition:delete', 'action' => 'delete', 'label' => 'Delete'],
                            ['key' => 'dental-condition:save', 'action' => 'save', 'label' => 'Save'],
                        ],
                    ],
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
                            ['key' => 'patient:delete', 'action' => 'delete', 'label' => 'Delete'],
                            ['key' => 'patient:save', 'action' => 'save', 'label' => 'Save'],
                        ],
                    ],
                    [
                        'id' => 'service',
                        'title' => 'Service',
                        'permissions' => [
                            ['key' => 'service:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'service:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'service:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'service:delete', 'action' => 'delete', 'label' => 'Delete'],
                            ['key' => 'service:save', 'action' => 'save', 'label' => 'Save'],
                        ],
                    ],
                    [
                        'id' => 'product',
                        'title' => 'Product',
                        'permissions' => [
                            ['key' => 'product:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'product:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'product:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'product:delete', 'action' => 'delete', 'label' => 'Delete'],
                            ['key' => 'product:save', 'action' => 'save', 'label' => 'Save'],
                        ],
                    ],
                    [
                        'id' => 'service-category',
                        'title' => 'Service Category',
                        'permissions' => [
                            ['key' => 'service-category:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'service-category:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'service-category:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'service-category:delete', 'action' => 'delete', 'label' => 'Delete'],
                            ['key' => 'service-category:save', 'action' => 'save', 'label' => 'Save'],
                        ],
                    ],
                    [
                        'id' => 'product-category',
                        'title' => 'Product Category',
                        'permissions' => [
                            ['key' => 'product-category:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'product-category:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'product-category:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'product-category:delete', 'action' => 'delete', 'label' => 'Delete'],
                            ['key' => 'product-category:save', 'action' => 'save', 'label' => 'Save'],
                        ],
                    ],
                    [
                        'id' => 'supplier',
                        'title' => 'Suppliers',
                        'permissions' => [
                            ['key' => 'supplier:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'supplier:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'supplier:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'supplier:delete', 'action' => 'delete', 'label' => 'Delete'],
                            ['key' => 'supplier:save', 'action' => 'save', 'label' => 'Save'],
                        ],
                    ],
                ],
            ],
            'quote' => [
                'id' => 'quote',
                'title' => 'Quote',
                'type' => 'group',
                'menus' => [
                    [
                        'id' => 'quote',
                        'title' => 'Quote',
                        'permissions' => [
                            ['key' => 'quotes:quotes:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'quotes:quotes:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'quotes:quotes:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'quotes:quotes:delete', 'action' => 'delete', 'label' => 'Delete'],
                            ['key' => 'quotes:quotes:view_detail', 'action' => 'view-detail', 'label' => 'View Detail'],
                            ['key' => 'quotes:quotes:approve', 'action' => 'approve', 'label' => 'Approve'],
                            ['key' => 'quotes:quotes:reject', 'action' => 'reject', 'label' => 'Reject'],
                            ['key' => 'quotes:quotes:print', 'action' => 'print', 'label' => 'Print'],
                        ],
                    ],
                ],
            ],
            'target' => [
                'id' => 'target',
                'title' => 'Target',
                'type' => 'group',
                'menus' => [
                    [
                        'id' => 'target-clinic',
                        'title' => 'Clinic Target',
                        'permissions' => [
                            ['key' => 'target-clinic:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'target-clinic:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'target-clinic:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'target-clinic:save', 'action' => 'save', 'label' => 'Save'],
                            ['key' => 'target-clinic:delete', 'action' => 'delete', 'label' => 'Delete'],
                        ],
                    ],
                    [
                        'id' => 'target-doctor',
                        'title' => 'Doctor Target',
                        'permissions' => [
                            ['key' => 'target-doctor:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'target-doctor:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'target-doctor:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'target-doctor:save', 'action' => 'save', 'label' => 'Save'],
                            ['key' => 'target-doctor:delete', 'action' => 'delete', 'label' => 'Delete'],
                        ],
                    ],
                ],
            ],
            'accounting' => [
                'id' => 'accounting',
                'title' => 'Accounting',
                'type' => 'group',
                'menus' => [
                    [
                        'id' => 'cash-deposit',
                        'title' => 'Cash Deposit',
                        'permissions' => [
                            ['key' => 'accounting:cashDeposit:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'accounting:cashDeposit:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'accounting:cashDeposit:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'accounting:cashDeposit:delete', 'action' => 'delete', 'label' => 'Delete'],
                            ['key' => 'accounting:cashDeposit:print', 'action' => 'print', 'label' => 'Print'],
                        ],
                    ],
                ],
            ],
            'invoice' => [
                'id' => 'invoice',
                'title' => 'Invoice',
                'type' => 'group',
                'menus' => [
                    [
                        'id' => 'invoice',
                        'title' => 'Invoice',
                        'permissions' => [
                            ['key' => 'invoice:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'invoice:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'invoice:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'invoice:delete', 'action' => 'delete', 'label' => 'Delete'],
                            ['key' => 'invoice:save', 'action' => 'save', 'label' => 'Save'],
                            ['key' => 'invoice:view-detail', 'action' => 'view-detail', 'label' => 'View Detail'],
                            ['key' => 'invoice:print', 'action' => 'print', 'label' => 'Print'],
                            ['key' => 'invoice:pay', 'action' => 'pay', 'label' => 'Pay'],
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
                            ['key' => 'role:delete', 'action' => 'delete', 'label' => 'Delete'],
                            ['key' => 'role:save', 'action' => 'save', 'label' => 'Save'],
                        ],
                    ],
                    [
                        'id' => 'doctor',
                        'title' => 'Doctor',
                        'permissions' => [
                            ['key' => 'doctor:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'doctor:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'doctor:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'doctor:delete', 'action' => 'delete', 'label' => 'Delete'],
                            ['key' => 'doctor:save', 'action' => 'save', 'label' => 'Save'],
                        ],
                    ],
                    [
                        'id' => 'user',
                        'title' => 'Users',
                        'permissions' => [
                            ['key' => 'user:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'user:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'user:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'user:delete', 'action' => 'delete', 'label' => 'Delete'],
                            ['key' => 'user:save', 'action' => 'save', 'label' => 'Save'],
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
                        'id' => 'general',
                        'title' => 'General Settings',
                        'permissions' => [
                            ['key' => 'settings:general:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'settings:general:edit', 'action' => 'edit', 'label' => 'Edit'],
                        ],
                    ],
                    [
                        'id' => 'medicine',
                        'title' => 'Medicines',
                        'permissions' => [
                            ['key' => 'medicine:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'medicine:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'medicine:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'medicine:delete', 'action' => 'delete', 'label' => 'Delete'],
                            ['key' => 'medicine:save', 'action' => 'save', 'label' => 'Save'],
                        ],
                    ],
                    [
                        'id' => 'prescription',
                        'title' => 'Prescriptions',
                        'permissions' => [
                            ['key' => 'prescription:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'prescription:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'prescription:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'prescription:delete', 'action' => 'delete', 'label' => 'Delete'],
                            ['key' => 'prescription:save', 'action' => 'save', 'label' => 'Save'],
                        ],
                    ],
                    [
                        'id' => 'payment-method',
                        'title' => 'Payment Methods',
                        'permissions' => [
                            ['key' => 'payment-method:view', 'action' => 'view', 'label' => 'View'],
                            ['key' => 'payment-method:create', 'action' => 'create', 'label' => 'Create'],
                            ['key' => 'payment-method:edit', 'action' => 'edit', 'label' => 'Edit'],
                            ['key' => 'payment-method:delete', 'action' => 'delete', 'label' => 'Delete'],
                            ['key' => 'payment-method:save', 'action' => 'save', 'label' => 'Save'],
                        ],
                    ],
                ],
            ],
        ];
    }
}
