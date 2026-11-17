<?php

namespace App\Models\Tenant;

use App\Models\UserTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reception extends Model
{
    use HasFactory, SoftDeletes;

    const CODE = 'prefix_reception';
    const CODEFORMAT = 'format_reception';
    const REGISTERED = 'registered';

    protected $table = 'receptions';

    protected $fillable = [
        'reception_number',
        'patient_id',
        'invoice_id',
        'doctor_id',
        'reception_date',
        'visit_type',
        'chief_complaint',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'reception_date' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(MedicalHistory::class, 'invoice_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(UserTenant::class, 'created_by');
    }

    public static function generateReceptionNumber(): string
    {
        $datePrefix = date('Ymd');
        $latest = self::where('reception_number', 'like', "REC-{$datePrefix}-%")
            ->orderByDesc('id')
            ->first();

        $sequence = 1;
        if ($latest && preg_match('/REC-\d+-(\d+)/', $latest->reception_number, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return sprintf('REC-%s-%04d', $datePrefix, $sequence);
    }
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (\Auth::check() && empty($model->created_by)) {
                $model->created_by = \Auth::user()->id; 
            }
            if (empty($model->attributes['reception_number'])) {
                $model->number();
            }
        });
    }

    protected function number() {
        $this->attributes['reception_number'] = (string) self::getNextReceptionNumber();
    }

    public static function getNextReceptionNumber(): int
    {
        try {
            $connection = (new static)->getConnection();
            $driver = $connection->getDriverName();

            if ($driver === 'pgsql') {
                $maxNum = static::withTrashed()
                    ->whereRaw("reception_number ~ '^[0-9]+$'")
                    ->selectRaw("COALESCE(MAX(CAST(reception_number AS BIGINT)), 0) as max_num")
                    ->value('max_num');
                $next = ((int) $maxNum) + 1;
            } else {
                $maxNum = static::withTrashed()
                    ->select('reception_number')
                    ->get()
                    ->filter(fn($r) => is_numeric($r->getRawOriginal('reception_number')))
                    ->map(fn($r) => (int) $r->getRawOriginal('reception_number'))
                    ->max() ?? 0;
                $next = ((int) $maxNum) + 1;
            }
        } catch (\Throwable $e) {
            $next = ((int) static::withTrashed()->count()) + 1;
        }

        // Guarantee collision safety against any record (including soft-deleted)
        while (static::withTrashed()->where('reception_number', (string) $next)->exists()) {
            $next++;
        }

        return $next;
    }

    public function getYearSeriesCodeAttribute()
    {
        $prefix = get_prefix_code('visit') ?: (
            !empty(self::CODE) && \Auth::check() ? (\Auth::user()?->settings(self::CODE) ?? '') : ''
        );
        $year = $this->reception_date ? $this->reception_date->format('Y') : date('Y');
        $pad_length = (int) (get_prefix_code('padLength') ?: 6);
        $rawNum = $this->attributes['reception_number'] ?? $this->id ?? 1;
        $num = is_numeric($rawNum) ? str_pad($rawNum, $pad_length, '0', STR_PAD_LEFT) : $rawNum;
        return $prefix ? "{$prefix}-{$year}-{$num}" : "{$year}-{$num}";
    }

    public function getReceptionNumberAttribute($value) {
        if (!empty($value) && !is_numeric($value)) {
            return $value;
        }

        $prefix = get_prefix_code('visit') ?: (
            !empty(self::CODE) && \Auth::check() ? (\Auth::user()?->settings(self::CODE) ?? '') : ''
        );
        $format = get_prefix_code(self::CODEFORMAT);
        $pad_length = (int) (get_prefix_code('padLength') ?: 6);
        $padded = str_pad((string) $value, $pad_length, '0', STR_PAD_LEFT);

        if ($prefix != "") {
            return $format ? $this->year_series_code : $prefix . $padded;
        }
        return $padded;
    }

    public function setReceptionNumberAttribute($value) {
        if (empty($value)) {
            $this->attributes['reception_number'] = (string) self::getNextReceptionNumber();
        } else {
            $this->attributes['reception_number'] = (string) $value;
        }
    }
}
