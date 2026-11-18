<?php

use Illuminate\Database\Migrations\Migration;
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

            // Diagnostic Legend (Conditions)
            'dental-condition:view',
            'dental-condition:create',
            'dental-condition:edit',
            'dental-condition:save',
            'dental-condition:delete',

            // Payment
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

            // Payment Methods
            'payment-method:view',
            'payment-method:create',
            'payment-method:edit',
            'payment-method:save',
            'payment-method:delete',
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
            'doctor:view', 'doctor:create', 'doctor:edit', 'doctor:save', 'doctor:delete',
            'user:view', 'user:create', 'user:edit', 'user:save', 'user:delete',
            'supplier:view', 'supplier:create', 'supplier:edit', 'supplier:save', 'supplier:delete',
            'dental-condition:view', 'dental-condition:create', 'dental-condition:edit', 'dental-condition:save', 'dental-condition:delete',
            'payment:view',
            'invoice:view', 'invoice:create', 'invoice:edit', 'invoice:save', 'invoice:delete', 'invoice:pay', 'invoice:print', 'invoice:view-detail', 'invoice:view_detail',
            'payment-method:view', 'payment-method:create', 'payment-method:edit', 'payment-method:save', 'payment-method:delete',
        ];

        Permission::where('guard_name', 'tenant')
            ->whereIn('name', $permissions)
            ->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
