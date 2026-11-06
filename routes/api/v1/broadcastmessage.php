<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\BroadcastMessageController;

Route::middleware([
      'api',
      'auth:sanctum',
      'verified',
      'log.route.api',
])->prefix('v1')->group(function () {
      Route::post('/list-event', [BroadcastMessageController::class, 'listEvents'])->name('api-v1-list-events');
});
