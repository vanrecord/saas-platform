<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Laravel\Jetstream\Http\Controllers\Inertia\UserProfileController;


// Route::middleware([
//     'web',
//     // InitializeTenancyByDomain::class,
//     // PreventAccessFromCentralDomains::class,
//     'auth:sanctum', 'verified'
// ])->group(function () {
//     dd('sdafsdfasdf');
//     // Route::resource('/', UserController::class);
//     // Route::resource('role', RoleController::class)->only([
//     //     'index', 'create','store','edit','update','destroy'
//     // ]);

//     // Route::get('/user/profile', [UserProfileController::class, 'show'])
//     //                 ->name('profile.show');

// });


