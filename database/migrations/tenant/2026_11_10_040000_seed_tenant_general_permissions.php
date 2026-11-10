<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            // Dashboard
            'dashboard:view',

            // Register
            'register:view',

            // Patient
            'patient:view',
            'patient:create',
            'patient:edit',
            'patient:save',
            'patient:delete',

            // Service
            'service:view',
            'service:create',
            'service:edit',
            'service:save',
            'service:delete',

            // Product
            'product:view',
            'product:create',
            'product:edit',
            'product:save',
            'product:delete',

            // Service Category
            'service-category:view',
            'service-category:create',
            'service-category:edit',
            'service-category:save',
            'service-category:delete',

            // Product Category
            'product-category:view',
            'product-category:create',
            'product-category:edit',
            'product-category:save',
            'product-category:delete',

            // Roles & Permissions
            'role:view',
            'role:create',
            'role:edit',
            'role:save',
            'role:delete',
        ];

        $now = now();
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'tenant'],
                ['created_at' => $now, 'updated_at' => $now]
            );
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'dashboard:view',
            'register:view',
            'patient:view', 'patient:create', 'patient:edit', 'patient:save', 'patient:delete',
            'service:view', 'service:create', 'service:edit', 'service:save', 'service:delete',
            'product:view', 'product:create', 'product:edit', 'product:save', 'product:delete',
            'service-category:view', 'service-category:create', 'service-category:edit', 'service-category:save', 'service-category:delete',
            'product-category:view', 'product-category:create', 'product-category:edit', 'product-category:save', 'product-category:delete',
            'role:view', 'role:create', 'role:edit', 'role:save', 'role:delete',
        ];

        Permission::where('guard_name', 'tenant')
            ->whereIn('name', $permissions)
            ->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
