<?php

namespace App\Models;

use App\Supports\Trait\CoreTrait;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Laravel\Jetstream\HasProfilePhoto;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use Stancl\Tenancy\Contracts\SyncMaster;
use Stancl\Tenancy\Database\Concerns\ResourceSyncing;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Stancl\Tenancy\Database\Models\TenantPivot;
use App\Models\UserTenant;
use DateTimeInterface;
use Baum\NestedSet\Node as WorksAsNestedSet;
use App\Models\Subscription;

class User extends Authenticatable implements SyncMaster, MustVerifyEmail
{
    use WorksAsNestedSet;
    use CoreTrait;
    use HasRoles;
    use HasDatabase, HasDomains;
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use ResourceSyncing, CentralConnection;
    

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'email_verified_at',
        'level_access',
        'parent_id',
        'left',
        'right',
        'depth',
        'phone',
        'global_id',
        'created_by',
        'updated_by',
        'user_type',
    ];

    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:d/m/Y H:i',
        'updated_at' => 'datetime:d/m/Y H:i',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
        'two_factor_enabled',
        'verified'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    const SUPER_ADMIN = 2;
    const ADMIN = 1;
    
    public function getTwoFactorEnabledAttribute($value)
    {
        return !is_null($this->two_factor_secret);
    }

    public function isSuperAdmin(){
        return $this->getRawOriginal('level_access')==self::SUPER_ADMIN??false;
    }

    public function isAdmin(){
        return $this->getRawOriginal('level_access')==self::ADMIN??false;
    }

    public function getLevelAccessAttribute($value)
    {
        return $value?true:false;
    }

    public function setLevelAccessAttribute($value)
    {
        $this->attributes['level_access'] = $value===2?2:($value?1:0);
    }

    public function getVerifiedAttribute($value)
    {
        return  !is_null($this->email_verified_at);
    }

    public function getPermissionArray()
    {
        $rawLevel = (int) ($this->getRawOriginal('level_access') ?? 0);
        if ($rawLevel === self::ADMIN || $rawLevel === self::SUPER_ADMIN || $this->isAdmin() || $this->isSuperAdmin()) {
            return ['all' => true]; // is admin
        } 
        return $this->getAllPermissions()->mapWithKeys(function ($pr) {
            return [$pr['name'] => true];
        });
    }

    public function getAllPermissions(): Collection
    {
        /** @var Collection $permissions */
        $permissions = $this->permissions;

        if ($this->roles) {
            $permissions = $this->getPermissionsViaRoles();
        }

        return $permissions->sort()->values();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrActive($query)
    {
        return $query->orWhere('is_active', true);
    }

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_users', 'global_user_id', 'tenant_id', 'global_id')
            ->using(TenantPivot::class);
    }

    public function getTenantModelName(): string
    {
        return UserTenant::class;
    }

    public function getGlobalIdentifierKey()
    {
        return $this->getAttribute($this->getGlobalIdentifierKeyName());
    }

    public function getGlobalIdentifierKeyName(): string
    {
        return 'global_id';
    }

    public function getCentralModelName(): string
    {
        return static::class;
    }

    public function getSyncedAttributeNames(): array
    {
        return [
            'name',
            'password',
            'email',
            'is_active',
        ];
    }

    public function domains()
    {
        return $this->hasMany(config('tenancy.domain_model'), 'user_id');
    }
    public function createdBy(){
       
        return $this->belongsTo(User::class,'created_by');
    }
    public function updatedBy(){
       
        return $this->belongsTo(User::class,'updated_by');
    }
    public function subscriptions(){
       
        return $this->belongsToMany(Subscription::class,'user_id');
    }

}
