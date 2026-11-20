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

            // Doctor
            'doctor:view',
            'doctor:create',
            'doctor:edit',
            'doctor:save',
            'doctor:delete',

            // Users
            'user:view',
            'user:create',
            'user:edit',
            'user:save',
            'user:delete',

            // Suppliers
            'supplier:view',
            'supplier:create',
            'supplier:edit',
            'supplier:save',
            'supplier:delete',

            // Diagnostic Legend (Dental Conditions)
            'dental-condition:view',
            'dental-condition:create',
            'dental-condition:edit',
            'dental-condition:save',
            'dental-condition:delete',

            // Payment (View Only)
            'payment:view',

            // Invoice
            'invoice:view',
            'invoice:create',
            'invoice:edit',
            'invoice:save',
            'invoice:delete',
            'invoice:pay',
            'invoice:print',
            'invoice:view-detail',
            'invoice:view_detail',

            // General Settings
            'settings:general:view',
            'settings:general:edit',
            'general:view',
            'general:edit',

            // Payment Methods
            'payment-method:view',
            'payment-method:create',
            'payment-method:edit',
            'payment-method:save',
            'payment-method:delete',

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

            // Quote
            'quote:view',
            'quote:create',
            'quote:edit',
            'quote:save',
            'quote:delete',
            'quote:view-detail',
            'quote:view_detail',
            'quote:approve',
            'quote:reject',
            'quote:print',
            'quotes:quotes:view',
            'quotes:quotes:create',
            'quotes:quotes:edit',
            'quotes:quotes:delete',
            'quotes:quotes:view_detail',
            'quotes:quotes:approve',
            'quotes:quotes:reject',
            'quotes:quotes:print',

            // Target (Clinic & Doctor)
            'target-clinic:view',
            'target-clinic:create',
            'target-clinic:edit',
            'target-clinic:save',
            'target-clinic:delete',
            'targets:clinic:view',
            'targets:clinic:create',
            'targets:clinic:edit',
            'targets:clinic:save',
            'targets:clinic:delete',
            'target-doctor:view',
            'target-doctor:create',
            'target-doctor:edit',
            'target-doctor:save',
            'target-doctor:delete',
            'targets:doctor:view',
            'targets:doctor:create',
            'targets:doctor:edit',
            'targets:doctor:save',
            'targets:doctor:delete',
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
