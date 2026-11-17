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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
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

        Permission::where('guard_name', 'tenant')
            ->whereIn('name', $permissions)
            ->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
