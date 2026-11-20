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
            'accounting:cashDeposit:view',
            'accounting:cashDeposit:create',
            'accounting:cashDeposit:edit',
            'accounting:cashDeposit:delete',
            'accounting:cashDeposit:print',
            'cash-deposit:view',
            'cash-deposit:create',
            'cash-deposit:edit',
            'cash-deposit:delete',
            'cash-deposit:print',
            'cash-deposit:save',
        ];

        // Remove verify permission if already present
        Permission::where('guard_name', 'tenant')
            ->whereIn('name', ['accounting:cashDeposit:verify', 'cash-deposit:verify'])
            ->delete();

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
            'accounting:cashDeposit:view',
            'accounting:cashDeposit:create',
            'accounting:cashDeposit:edit',
            'accounting:cashDeposit:delete',
            'accounting:cashDeposit:print',
            'cash-deposit:view',
            'cash-deposit:create',
            'cash-deposit:edit',
            'cash-deposit:delete',
            'cash-deposit:print',
            'cash-deposit:save',
        ];

        Permission::where('guard_name', 'tenant')
            ->whereIn('name', array_merge($permissions, ['accounting:cashDeposit:verify', 'cash-deposit:verify']))
            ->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
