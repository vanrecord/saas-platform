<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Controllers\Tenant\Auth\AuthenticatedSessionController as TenantAuthController;
use App\Http\Controllers\Tenant\TenantDashboardController;
use App\Http\Controllers\Tenant\ReceptionController;
use App\Http\Controllers\Tenant\DentalViewController;
use App\Http\Controllers\Tenant\DentalConditionController;
use App\Http\Controllers\Tenant\PatientController;
use App\Http\Controllers\Tenant\SupplierController;
use App\Http\Controllers\Tenant\ServiceController;
use App\Http\Controllers\Tenant\ProductController;
use App\Http\Controllers\Tenant\RoleController;
use App\Http\Controllers\Tenant\DoctorController;
use App\Http\Controllers\Tenant\UserController;
use App\Http\Controllers\Tenant\QuoteController;
use App\Http\Controllers\Tenant\InvoiceController;
use App\Http\Controllers\Tenant\InvoicePrefixController;
use App\Http\Controllers\Tenant\GeneralSettingController;
use App\Http\Controllers\Tenant\MedicineController;
use App\Http\Controllers\Tenant\PrescriptionController;
use App\Http\Controllers\Tenant\CashDepositController;
use App\Http\Controllers\Tenant\ClinicTargetController;
use App\Http\Controllers\Tenant\DoctorTargetController;
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
        Route::get('/register', [ReceptionController::class, 'create'])->name('tenant.visit.create');
        Route::post('/register', [ReceptionController::class, 'store'])->name('tenant.visit.store');

        // Interactive Odontogram
        Route::get('/dental', [DentalViewController::class, 'index'])->name('dental.index');
        Route::get('/dental/{dentalChart}', [DentalViewController::class, 'show'])->name('dental.show');

        // Dental Diagnostic Conditions
        Route::resource('dental-conditions', DentalConditionController::class);

        // Patients Directory
        Route::resource('patients', PatientController::class);
        Route::post('patients/{patient}', [PatientController::class, 'update'])->name('patients.update-post');

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
        Route::post('doctors/{doctor}', [DoctorController::class, 'update'])->name('doctors.update-post');

        // Tenant Users
        Route::resource('users', UserController::class);
        Route::post('users/{user}', [UserController::class, 'update'])->name('users.update-post');

        // Quote Management
        Route::resource('quotes', QuoteController::class);
        Route::get('/quotes/{quote}/print', [QuoteController::class, 'print'])->name('quotes.print');
        Route::post('/quotes/{quote}/items/{item}/approve', [QuoteController::class, 'approveItem'])->name('quotes.items.approve');
        Route::post('/quotes/{quote}/items/{item}/reject', [QuoteController::class, 'rejectItem'])->name('quotes.items.reject');
        Route::post('/quotes/{quote}/items', [QuoteController::class, 'addItem'])->name('quotes.items.add');

        // Invoice & Billing
        Route::resource('invoices', InvoiceController::class);
        Route::get('/tenant/invoices', [InvoiceController::class, 'index'])->name('tenant.invoices.index');
        Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
        Route::get('/invoices/{invoice}/payment-history', [InvoiceController::class, 'paymentHistory'])->name('tenant.invoices.payment-history');
        Route::post('/invoices/{invoice}/payments', [InvoiceController::class, 'storePayment'])->name('tenant.invoices.payments.store');
        Route::get('/invoices/{invoice}/statement', [InvoiceController::class, 'statement'])->name('tenant.invoices.statement');
        // Payments
        Route::get('/payments', [PaymentController::class, 'index'])->name('tenant.payments.index');
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('tenant.payments.destroy');
        Route::get('/payments/{payment}/print', [PaymentController::class, 'printReceipt'])->name('tenant.payments.print');
        Route::get('/api/payments/patients-search', [PaymentController::class, 'searchPatients'])->name('tenant.payments.patients-search');

        // Cash Deposits (Accounting)
        Route::prefix('cash-deposits')->name('cash-deposits.')->group(function () {
            Route::get('/', [CashDepositController::class, 'index'])->name('index');
            Route::post('/', [CashDepositController::class, 'store'])->name('store');
            Route::get('/patient-search', [CashDepositController::class, 'patientSearch'])->name('patient-search');
            Route::get('/patient-details/{patient_id}', [CashDepositController::class, 'patientDetails'])->name('patient-details');
            Route::get('/credit-balance', [CashDepositController::class, 'getCreditBalance'])->name('credit-balance');
            Route::get('/{fundTransfer}', [CashDepositController::class, 'show'])->name('show');
            Route::put('/{fundTransfer}', [CashDepositController::class, 'update'])->name('update');
            Route::delete('/{fundTransfer}', [CashDepositController::class, 'destroy'])->name('destroy');
            Route::post('/{fundTransfer}/verify', [CashDepositController::class, 'verify'])->name('verify');
            Route::get('/{fundTransfer}/print', [CashDepositController::class, 'print'])->name('print');
        });

        // Legacy Cash Deposit Route Aliases
        Route::post('/cash-deposit/store', [CashDepositController::class, 'store'])->name('cash-deposit.store');
        Route::get('/cash-deposit/{patient_id}', [CashDepositController::class, 'patientDetails'])->name('cash-deposit.show');

        // General Settings
        Route::get('settings/general', [GeneralSettingController::class, 'index'])->name('settings.general.index');
        Route::post('settings/general', [GeneralSettingController::class, 'updateClinicInfo'])->name('settings.general.update-clinic');
        Route::post('settings/general/doctor-except', [GeneralSettingController::class, 'updateDoctorExcept'])->name('settings.general.update-doctor-except');

        // Invoice Prefix Settings
        Route::resource('settings/invoice-prefixes', InvoicePrefixController::class)->names('settings.invoice-prefixes');

        // Payment Method Settings
        Route::resource('settings/payment-methods', PaymentMethodController::class)->names('settings.payment-methods');
        Route::post('settings/payment-methods/{payment_method}', [PaymentMethodController::class, 'update'])->name('settings.payment-methods.update-post');

        // Medicine Settings / Management
        Route::resource('medicines', MedicineController::class)->except(['show']);

        // Prescription Management
        Route::get('/prescriptions/{prescription}/print', [PrescriptionController::class, 'print'])->name('prescriptions.print');
        Route::get('/prescriptions/patient-histories/{patient}', [PrescriptionController::class, 'patientHistories'])->name('prescriptions.patient-histories');
        Route::resource('prescriptions', PrescriptionController::class);

        // Target Management — Clinic and Doctor
        Route::prefix('targets')->name('targets.')->group(function () {
            Route::resource('clinic', ClinicTargetController::class)->names('clinic');
            Route::resource('doctor', DoctorTargetController::class)->names('doctor');
        });

        // API Endpoints for Frontend Interactive Chart & AJAX
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('medicines', function () {
                return response()->json(\App\Models\Tenant\Medicine::orderBy('name')->get(['id', 'name']));
            })->name('medicines.list');
            Route::get('patients/search', [ReceptionController::class, 'searchPatients'])->name('patients.search');
            Route::post('patients/quick-create', [ReceptionController::class, 'quickCreatePatient'])->name('patients.quick-create');
            Route::get('patients/{patient}/invoices', [ReceptionController::class, 'getPatientInvoices'])->name('patients.invoices');
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
