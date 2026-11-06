<?php

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Tenant\Auth\AuthenticatedSessionController as TenantAuthController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\CommuneController;
use App\Http\Controllers\UserLogController;
use App\Http\Controllers\VillageController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\ClientNetworkController;
use App\Http\Controllers\LogAccessRequestApiController;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;

/*
|--------------------------------------------------------------------------
| Web Routes (Unified Central & Tenant Landing / Authentication)
|--------------------------------------------------------------------------
|
| Handles entry point / login / logout dispatching dynamically based on host,
| preventing route name collisions in Ziggy and ensuring no cross-origin errors.
|
*/

Route::middleware(['web'])->group(function () {
    // Landing page
    Route::get('/', function (Request $request) {
        $centralDomains = config('tenancy.central_domains', []);
        if (! in_array($request->getHost(), $centralDomains, true)) {
            if (function_exists('tenancy') && ! tenancy()->initialized) {
                $tenant = app(DomainTenantResolver::class)->resolve($request->getHost());
                tenancy()->initialize($tenant);
            }
            config(['auth.defaults.guard' => 'tenant']);
            if (auth('tenant')->check()) {
                return redirect()->route('tenant.dashboard');
            }
            return app(TenantAuthController::class)->create();
        }
        return redirect()->route('login');
    })->name('frontend.index');

    // Public Central Pages
    Route::controller(FAQController::class)->group(function () {
        Route::get('/bms-faq', 'index')->name('frontend.bms-faq');
    });

    Route::controller(ClientNetworkController::class)->group(function () {
        Route::get('/network-provider/{id}', 'provider')->name('frontend.client-network-provider');
    });

    // Authentication Routes
    Route::get('/login', function (Request $request) {
        $centralDomains = config('tenancy.central_domains', []);
        if (! in_array($request->getHost(), $centralDomains, true)) {
            if (function_exists('tenancy') && ! tenancy()->initialized) {
                $tenant = app(DomainTenantResolver::class)->resolve($request->getHost());
                tenancy()->initialize($tenant);
            }
            config(['auth.defaults.guard' => 'tenant']);
            return app(TenantAuthController::class)->create();
        }
        return app(AuthenticatedSessionController::class)->create();
    })->name('login');

    Route::post('/login', function (Request $request) {
        $centralDomains = config('tenancy.central_domains', []);
        if (! in_array($request->getHost(), $centralDomains, true)) {
            if (function_exists('tenancy') && ! tenancy()->initialized) {
                $tenant = app(DomainTenantResolver::class)->resolve($request->getHost());
                tenancy()->initialize($tenant);
            }
            config(['auth.defaults.guard' => 'tenant']);
            return app(TenantAuthController::class)->store($request);
        }
        return app(AuthenticatedSessionController::class)->store($request);
    })->name('login.store');

    Route::post('/logout', function (Request $request) {
        $centralDomains = config('tenancy.central_domains', []);
        if (! in_array($request->getHost(), $centralDomains, true)) {
            if (function_exists('tenancy') && ! tenancy()->initialized) {
                $tenant = app(DomainTenantResolver::class)->resolve($request->getHost());
                tenancy()->initialize($tenant);
            }
            config(['auth.defaults.guard' => 'tenant']);
            return app(TenantAuthController::class)->destroy($request);
        }
        return app(AuthenticatedSessionController::class)->destroy($request);
    })->name('logout');
});

/*
|--------------------------------------------------------------------------
| Authenticated Central SaaS Administration Routes
|--------------------------------------------------------------------------
|
| Scoped strictly to central users. Protected by 'central.domain' middleware
| so tenant subdomains are rejected with a 404 response.
|
*/
Route::middleware([
    'web',
    'central.domain',
    'auth:sanctum,web',
])->group(function () {
    Route::get('/admin', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard.index');

    Route::resource('/user', UserController::class);
    Route::post('/user/email_verified/{id}', [UserController::class, 'emailVerified'])->name('user.email_verified');
    Route::post('/user/reset/{id}', [UserController::class, 'resetPassword'])->name('user.reset');

    Route::controller(DomainController::class)->group(function () {
        Route::get('/domain', 'index')->name('domain.index');
        Route::get('/domain/create', 'create')->name('domain.create');
        Route::post('/domain', 'store')->name('domain.store');
        Route::get('/domain/{id}/edit', 'edit')->name('domain.edit');
        Route::put('/domain/{id}', 'update')->name('domain.update');
        Route::post('/domain/clear-data', 'clearData')->name('domain.clear-data');
        Route::post('/domain/delete', 'destroy')->name('domain.destroy');
        Route::post('/domain/maintenance-mode', 'maintenanceMode')->name('domain.maintenance-mode');
        Route::post('/domain/backup', 'backup')->name('domain.backup');
    });

    Route::get('district/{id}', [DomainController::class, 'listDistrict'])->name('domain.district');
    Route::get('/log-activity', [UserLogController::class, 'index'])->name('log-activity.index');
    Route::resource('/province', ProvinceController::class);
    Route::resource('/district', DistrictController::class);
    Route::resource('/commune', CommuneController::class);
    Route::resource('/village', VillageController::class);

    Route::get('log-access-request-api', [LogAccessRequestApiController::class, 'index'])->name('log-access-request-api.index');

    // Route::resource('/role', RoleController::class)->only([
    //     'index', 'create', 'store', 'edit', 'update', 'destroy'
    // ]);
    Route::redirect('/role', '/user');

    Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
});
