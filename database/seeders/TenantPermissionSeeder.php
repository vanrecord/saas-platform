<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class TenantPermissionSeeder extends Seeder
{
    public function run(): void
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

            // Medicines
            'medicine:view',
            'medicine:create',
            'medicine:edit',
            'medicine:save',
            'medicine:delete',
            'medicines:medicines:view',
            'medicines:medicines:create',
            'medicines:medicines:edit',
            'medicines:medicines:delete',

            // Prescriptions
            'prescription:view',
            'prescription:create',
            'prescription:edit',
            'prescription:save',
            'prescription:delete',
            'prescriptions:prescriptions:view',
            'prescriptions:prescriptions:create',
            'prescriptions:prescriptions:edit',
            'prescriptions:prescriptions:delete',
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
}
