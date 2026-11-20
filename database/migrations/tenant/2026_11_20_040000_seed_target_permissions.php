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
            // Clinic Target
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

            // Doctor Target
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
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

        Permission::where('guard_name', 'tenant')
            ->whereIn('name', $permissions)
            ->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
