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
use Stancl\Tenancy\Contracts\Syncable;
use Stancl\Tenancy\Database\Concerns\ResourceSyncing;
use App\Models\User;

class UserTenant extends Authenticatable implements Syncable
{

    use CoreTrait;
    use HasRoles;
    use HasDatabase, HasDomains;
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use ResourceSyncing;

    public $table = 'users';

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
        'level_access',
        'phone',
        'global_id'
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
        'created_at' => 'datetime:d/m/Y H:i'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
        'two_factor_enabled',
    ];

    const ADMIN = 1;
    const SUPER_ADMIN = 2;
    const USER_DEV = 3;
    
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
        // $this->attributes['level_access'] = $value==2?2:($value?1:0);
        $this->attributes['level_access'] = !is_bool($value)?$value:($value?1:0);
    }

    // public static function booted()
    // {
    //     static::creating(function ($user) {
    //         $user->password = Hash::make($user->password);
    //     });
    // }

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
        return User::class;
    }

    public function hospitals()
    {
        return $this->hasMany(Hospital::class, 'user_id');
    }

    public function hospital()
    {
        return $this->hasOne(Hospital::class, 'user_id');
    }

    public function getSyncedAttributeNames(): array
    {
        return [
            'name',
            'password',
            'email',
        ];
    }
}
