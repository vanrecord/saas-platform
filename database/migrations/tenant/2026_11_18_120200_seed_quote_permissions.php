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
            'quotes:quotes:view',
            'quotes:quotes:create',
            'quotes:quotes:edit',
            'quotes:quotes:delete',
            'quotes:quotes:view_detail',
            'quotes:quotes:approve',
            'quotes:quotes:reject',
            'quotes:quotes:print',
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
            'quotes:quotes:view',
            'quotes:quotes:create',
            'quotes:quotes:edit',
            'quotes:quotes:delete',
            'quotes:quotes:view_detail',
            'quotes:quotes:approve',
            'quotes:quotes:reject',
            'quotes:quotes:print',
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
        ];

        Permission::where('guard_name', 'tenant')
            ->whereIn('name', $permissions)
            ->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
