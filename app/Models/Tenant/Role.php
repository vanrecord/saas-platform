<?php

namespace App\Models\Tenant;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $guard_name = 'tenant';

    protected $fillable = [
        'name',
        'guard_name',
    ];

    /**
     * Checks if this is a system/protected role that should not be deleted.
     */
    public function isProtected(): bool
    {
        return in_array(strtolower($this->name), ['owner', 'super-admin', 'super admin', 'admin']);
    }
}
