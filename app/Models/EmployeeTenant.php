<?php

namespace App\Models;

use App\Models\CoreBase;
use App\Models\Position;
use App\Models\Shift;
use App\Models\FaceScan;
use App\Models\Mission;
use App\Models\MissionEmployee;
use App\Models\Attendance;
use App\Models\AttendanceMission;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\PositionRepository;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Group;
use App\Models\User;
use App\Supports\Trait\CoreTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use App\Models\EmployeeContract;
use App\Models\Addressable;
use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia;
use App\Supports\Trait\MediaLibrary;
use App\Supports\Trait\EhealthApi;
use App\Models\Department;
use App\Models\BroadcastMessage;

class EmployeeTenant extends CoreBase implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use CoreTrait;
    use MediaLibrary;

    protected $mediaImage = ['profile_employee','card_front','card_back','photo_face_scan','signature_employee'];
    protected $table = 'employees';
    protected $fillable = [
        'user_id',
        'name_native',
        'name_international',
        'dob',
        'sex',
        'phone',
        'email',
        'is_active',
        'created_by',
        'updated_by',
        'ehealth_code',
        'id_card',
        'valid_id_card_from',
        'valid_id_card_to',
        'data',
        'ehealth_connection',
        'locked_phone',
        'department_id',
        'is_membership',
        'device_token',
        'manager_id',
        'is_allow_check_in_out_attendance',
        'ehealth_email',
    ];

    const NOTCONNECT = 1;
    const TRIAL      = 2;
    const OFFICIAL   = 3;
    const DELETED    = 4;

}
