<?php 
namespace App\Models;
use App\Models\District;
use App\Models\Province;
use App\Models\User;
use DateTimeInterface;
use App\Supports\Trait\CoreTrait;
use Stancl\Tenancy\Database\Models\Domain as ModelDomain;
use Baum\NestedSet\Node as WorksAsNestedSet;

class Domain extends ModelDomain
{
    use CoreTrait;
    use WorksAsNestedSet;
    
    protected $fillable = [
        'company',
        'domain',
        'user_id',
        'tenant_id',
        'created_at',
        "updated_at",
        'is_active',
        'site_number',
        'parent_id',
        'left',
        'right',
        'depth',
        'package_id',
        'sector_id',
        'phone',
        'allow_create_domain',
        'range',
        'latlng',
        'province_id',
        'district_id',
        'created_by',
        'updated_by',
        'ehealth_organization_code',
    ];
    protected $casts = [
        'created_at' => 'datetime:d/m/Y H:i',
        'updated_at' => 'datetime:d/m/Y H:i'
    ];
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    public function createdBy(){
       
        return $this->belongsTo(User::class,'created_by');
    }
    public function updatedBy(){
       
        return $this->belongsTo(User::class,'updated_by');
    }

    public function scopeFilterUser($query)
    {
        $user = \Auth::user() ?? \Auth::guard('tenant')->user();
        if($user && method_exists($user, 'isSuperAdmin') && !$user->isSuperAdmin() && method_exists($user, 'isAdmin') && !$user->isAdmin()){
            return $query->whereIn('user_id',$user->getDescendantsAndSelf()->pluck('id')->toArray());
        }
    }

    public function package(){
        return $this->belongsTo(Package::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }
}