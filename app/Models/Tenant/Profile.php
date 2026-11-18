<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Profile extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'profiles';

    protected $fillable = [
        'firstname',
        'lastname',
        'gender',
        'facebook_username',
        'facebook',
        'google_username',
        'google',
        'twitter_username',
        'twitter',
        'linkdin_username',
        'linkdin',
        'website',
        'description',
        'fax',
        'primary_mobile',
        'secondary_mobile',
        'primary_telephone',
        'secondary_telephone',
        'date_of_birth',
        'avatar',
        'is_completed',
        'profileable_id',
        'profileable_type',
        'is_publish',
        'ehealth_is_doctor_appointment',
        'ehealth_consult_type',
    ];

    protected $casts = [
        'is_publish' => 'boolean',
        'ehealth_is_doctor_appointment' => 'boolean',
        'is_completed' => 'boolean',
        'date_of_birth' => 'date:Y-m-d',
    ];

    protected $appends = [
        'avatar_url',
        'full_name',
    ];

    public function profileable(): MorphTo
    {
        return $this->morphTo();
    }

    public function setEhealthConsultTypeAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['ehealth_consult_type'] = json_encode($value);
        } elseif (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) {
                $this->attributes['ehealth_consult_type'] = $value;
            } else {
                $this->attributes['ehealth_consult_type'] = json_encode($value);
            }
        } else {
            $this->attributes['ehealth_consult_type'] = null;
        }
    }

    public function getEhealthConsultTypeAttribute($value)
    {
        if (empty($value)) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode($value, true);
        if (is_string($decoded)) {
            $decodedAgain = json_decode($decoded, true);
            return is_array($decodedAgain) ? $decodedAgain : [];
        }

        return is_array($decoded) ? $decoded : [];
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->firstname} {$this->lastname}");
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->useDisk('public')
            ->singleFile();
    }

    public function getAvatarUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('avatar');
        if ($media) {
            if ($media->disk === 'public' || $media->disk === 'media') {
                return assetStorage($media->getUrl());
            }
            return $media->getTemporaryUrl(now()->addMinutes(120));
        }

        $avatar = $this->avatar ?? ($this->attributes['avatar'] ?? null);
        if (empty($avatar)) {
            return null;
        }

        if (filter_var($avatar, FILTER_VALIDATE_URL)) {
            return $avatar;
        }

        return assetStorage(Storage::disk('public')->url($avatar));
    }
}
