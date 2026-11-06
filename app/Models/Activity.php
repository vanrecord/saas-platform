<?php

namespace App\Models;

use App\Models\CoreBase;
use Illuminate\Database\Eloquent\Relations\MorphTo;


class Activity extends CoreBase
{
    protected $table = 'activity_log';
    protected $casts = [
        'properties' => 'collection',
    ];

    public function causer(): MorphTo
    {
        return $this->morphTo();
    }
}
