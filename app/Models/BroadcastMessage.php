<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Supports\Trait\CoreTrait;
use Spatie\MediaLibrary\HasMedia;
use App\Supports\Trait\MediaLibrary;
use Carbon\Carbon;
use App\Models\CoreBase;
use App\Models\User;
use Stancl\VirtualColumn\VirtualColumn;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\BroadcastMessageReminder;

class BroadcastMessage extends CoreBase implements HasMedia
{
    use HasFactory;
    use CoreTrait;
    use MediaLibrary;
    use VirtualColumn;

    protected $mediaImage = ['file_telegram_broadcast','image_event_broadcast'];
    
    protected $guarded = [];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'message',
            'start_date',
            'status',
            'category_id',
            'media_location',
            'created_by',
            'updated_by',
            'type',
            'title',
            'visibility',
            'module',
            'allow_accept', 
            'tenant_id',
            'broadcast_message_tenant_id',
            'created_at',
            'updated_at',
            'is_required_accept',
            'expire_accept_date',
        ];
    }
    public static function getDataColumn(): string
    {
        return 'data';
    }

    const DRAFT   = 0;
    const PROCESSING= 1; 
    const COMPLETED = 2;

    // Type
    const TELEGRAM = 1;
    const EHEALTH  = 2;

    // Visibility
    const PRIVATE = 1;
    const PUBLIC  = 2;

    // Module
    const HR     = 1;
    protected $appends = [
        'image_event_broadcast_url',
    ];
    public function createdBy(){
       
        return $this->belongsTo(User::class,'created_by');
    }

    public function updatedBy(){
       
        return $this->belongsTo(User::class,'updated_by');
    }

    public function broadcastMessageReminders()
    {
        return $this->hasMany(BroadcastMessageReminder::class);
    }
    
    public function broadcastEvent()
    {
        return $this->where('type', BroadcastMessage::EHEALTH);    
    }
    public function getImageEventBroadcastUrllAttribute() 
    {
        return $this->getFirstTemporaryUrl(Carbon::now()->addMinutes(10),'image_event_broadcast');
    }
    protected function startDate():Attribute
    {
        return Attribute::make(
            set: fn ($value) => Carbon::parse($value)->format('Y-m-d H:i:s'),
            get: fn ($value) => Carbon::parse($value)->format('d/m/Y H:i')
        );

    }

    protected function status():Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value,
            get: function ($value) {
                if($value==Self::DRAFT){
                    return 'Draft';
                }elseif($value==Self::PROCESSING){
                    return 'Processing';
                }elseif($value==Self::COMPLETED){
                    return 'Completed';
                }
            }
        );
    }
}
