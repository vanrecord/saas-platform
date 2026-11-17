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
            'medicine:view',
            'medicine:create',
            'medicine:edit',
            'medicine:save',
            'medicine:delete',
            'medicines:medicines:view',
            'medicines:medicines:create',
            'medicines:medicines:edit',
            'medicines:medicines:delete',
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
            'medicine:view',
            'medicine:create',
            'medicine:edit',
            'medicine:save',
            'medicine:delete',
            'medicines:medicines:view',
            'medicines:medicines:create',
            'medicines:medicines:edit',
            'medicines:medicines:delete',
        ];

        Permission::where('guard_name', 'tenant')
            ->whereIn('name', $permissions)
            ->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
