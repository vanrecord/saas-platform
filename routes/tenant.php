<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Controllers\Tenant\Auth\AuthenticatedSessionController as TenantAuthController;
use App\Http\Controllers\Tenant\TenantDashboardController;
use App\Http\Controllers\Tenant\PatientVisitRegistrationController;
use App\Http\Controllers\Tenant\DentalViewController;
use App\Http\Controllers\Tenant\DentalConditionController;
use App\Http\Controllers\Tenant\PatientController;
use App\Http\Controllers\Tenant\SupplierController;
use App\Http\Controllers\Tenant\ServiceController;
use App\Http\Controllers\Tenant\ProductController;
use App\Http\Controllers\Tenant\RoleController;
use App\Http\Controllers\Tenant\DoctorController;
use App\Http\Controllers\Tenant\UserController;
use App\Http\Controllers\Tenant\InvoiceController;
use App\Http\Controllers\Tenant\InvoicePrefixController;
use App\Http\Controllers\Tenant\PaymentController;
use App\Http\Controllers\Tenant\PaymentMethodController;
use App\Http\Controllers\Tenant\ServiceCategoryController;
use App\Http\Controllers\Tenant\ProductCategoryController;
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

        // Logs
        Route::get('/logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->name('tenant.logs');

        // Patient Visit Registration (Main Reference UI)
        Route::get('/register', [PatientVisitRegistrationController::class, 'create'])->name('tenant.visit.create');
        Route::post('/register', [PatientVisitRegistrationController::class, 'store'])->name('tenant.visit.store');

        // Interactive Odontogram
        Route::get('/dental', [DentalViewController::class, 'index'])->name('dental.index');
        Route::get('/dental/{dentalChart}', [DentalViewController::class, 'show'])->name('dental.show');

        // Dental Diagnostic Conditions
        Route::resource('dental-conditions', DentalConditionController::class);

        // Patients Directory
        Route::resource('patients', PatientController::class);

        // Hospital Suppliers
        Route::resource('suppliers', SupplierController::class);

        // Hospital Services
        Route::resource('services', ServiceController::class);

        // Hospital Service Categories
        Route::resource('service-categories', ServiceCategoryController::class);

        // Hospital Product Categories
        Route::resource('product-categories', ProductCategoryController::class);

        // Hospital Products
        Route::resource('products', ProductController::class);

        // Roles & Permissions
        Route::resource('roles', RoleController::class);

        // Tenant Doctors
        Route::resource('doctors', DoctorController::class);

        // Tenant Users
        Route::resource('users', UserController::class);

        // Invoice & Billing
        Route::resource('invoices', InvoiceController::class);
        Route::get('/tenant/invoices', [InvoiceController::class, 'index'])->name('tenant.invoices.index');
        Route::get('/invoices/{invoice}/payment-history', [InvoiceController::class, 'paymentHistory'])->name('tenant.invoices.payment-history');
        Route::post('/invoices/{invoice}/payments', [InvoiceController::class, 'storePayment'])->name('tenant.invoices.payments.store');
        Route::get('/invoices/{invoice}/statement', [InvoiceController::class, 'statement'])->name('tenant.invoices.statement');
        // Payments
        Route::get('/payments', [PaymentController::class, 'index'])->name('tenant.payments.index');
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('tenant.payments.destroy');
        Route::get('/payments/{payment}/print', [PaymentController::class, 'printReceipt'])->name('tenant.payments.print');
        Route::get('/api/payments/patients-search', [PaymentController::class, 'searchPatients'])->name('tenant.payments.patients-search');

        // Invoice Prefix Settings
        Route::resource('settings/invoice-prefixes', InvoicePrefixController::class)->names('settings.invoice-prefixes');

        // Payment Method Settings
        Route::resource('settings/payment-methods', PaymentMethodController::class)->names('settings.payment-methods');
        Route::post('settings/payment-methods/{payment_method}', [PaymentMethodController::class, 'update'])->name('settings.payment-methods.update-post');

        // API Endpoints for Frontend Interactive Chart & AJAX
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('patients/search', [PatientVisitRegistrationController::class, 'searchPatients'])->name('patients.search');
            Route::post('patients/quick-create', [PatientVisitRegistrationController::class, 'quickCreatePatient'])->name('patients.quick-create');
            Route::get('patients/{patient}/invoices', [PatientVisitRegistrationController::class, 'getPatientInvoices'])->name('patients.invoices');
            Route::get('address/provinces', [SupplierController::class, 'getProvinces'])->name('address.provinces');
            Route::get('address/districts/{provinceId}', [SupplierController::class, 'getDistricts'])->name('address.districts');
            Route::get('address/communes/{districtId}', [SupplierController::class, 'getCommunes'])->name('address.communes');
            Route::get('address/villages/{communeId}', [SupplierController::class, 'getVillages'])->name('address.villages');
            Route::apiResource('patients', ApiPatientController::class);
            Route::apiResource('tooth-findings', ApiToothFindingController::class);
            Route::apiResource('dental-conditions', ApiDentalConditionController::class);
        });
    });
});
