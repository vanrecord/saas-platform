<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Baum\NestedSet\Node as WorksAsNestedSet;

class RebuildDomain extends Model
{ 
    protected $table = 'domains';
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
        'ehealth_organization_code',
    ];
}