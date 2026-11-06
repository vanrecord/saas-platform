<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Controllers\Tenant\Auth\AuthenticatedSessionController as TenantAuthController;
use App\Http\Controllers\Tenant\TenantDashboardController;
use App\Http\Controllers\Tenant\PatientVisitRegistrationController;
use App\Http\Controllers\Tenant\DentalViewController;
use App\Http\Controllers\Tenant\DentalChartController;
use App\Http\Controllers\Tenant\DentalConditionController;
use App\Http\Controllers\Tenant\PatientController;
use App\Http\Controllers\Tenant\Api\DentalChartController as ApiDentalChartController;
use App\Http\Controllers\Tenant\Api\ChartToothController as ApiChartToothController;
use App\Http\Controllers\Tenant\Api\ToothFindingController as ApiToothFindingController;
use App\Http\Controllers\Tenant\Api\DentalConditionController as ApiDentalConditionController;
use App\Http\Controllers\Tenant\Api\PatientController as ApiPatientController;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Scoped strictly to tenant domains.
| Tenancy identification is enforced via InitializeTenancyByDomain.
| Central domains are blocked via PreventAccessFromCentralDomains.
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    \App\Http\Middleware\CheckTenantMaintenanceMode::class,
    \App\Http\Middleware\SetTenantAuthGuard::class,
])->group(function () {

    Route::middleware([
        'auth:tenant,sanctum,web',
    ])->group(function () {
        // Tenant Dashboard
        Route::get('/dashboard', [TenantDashboardController::class, 'index'])->name('tenant.dashboard');

        // Patient Visit Registration (Main Reference UI)
        Route::get('/register', [PatientVisitRegistrationController::class, 'create'])->name('tenant.visit.create');
        Route::post('/register', [PatientVisitRegistrationController::class, 'store'])->name('tenant.visit.store');

        // Interactive Odontogram
        Route::get('/dental', [DentalViewController::class, 'index'])->name('dental.index');
        Route::get('/dental/{dentalChart}', [DentalViewController::class, 'show'])->name('dental.show');

        // Dental Charts Management
        Route::resource('dental-charts', DentalChartController::class);

        // Dental Diagnostic Conditions
        Route::resource('dental-conditions', DentalConditionController::class);

        // Patients Directory
        Route::resource('patients', PatientController::class);

        // Billing & Finance (Placeholder views for sidebar completeness)
        Route::get('/invoices', [PatientVisitRegistrationController::class, 'create'])->name('tenant.invoices.index');
        Route::get('/payments', [PatientVisitRegistrationController::class, 'create'])->name('tenant.payments.index');

        // API Endpoints for Frontend Interactive Chart & AJAX
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('patients/search', [PatientVisitRegistrationController::class, 'searchPatients'])->name('patients.search');
            Route::post('patients/quick-create', [PatientVisitRegistrationController::class, 'quickCreatePatient'])->name('patients.quick-create');
            Route::apiResource('patients', ApiPatientController::class);
            Route::apiResource('dental-charts', ApiDentalChartController::class);
            Route::post('dental-charts/{dentalChart}/sync-findings', [ApiDentalChartController::class, 'syncFindings'])->name('dental-charts.sync-findings');
            Route::apiResource('chart-teeth', ApiChartToothController::class);
            Route::apiResource('tooth-findings', ApiToothFindingController::class);
            Route::apiResource('dental-conditions', ApiDentalConditionController::class);
        });
    });
});
