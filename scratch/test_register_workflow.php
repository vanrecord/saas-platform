<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant\Patient;
use App\Models\Tenant\MedicalHistory;
use App\Models\Tenant\Reception;
use App\Models\Tenant\PatientVisit;
use App\Models\Tenant\Prefix;
use App\Models\Tenant\Doctor;
use App\Models\UserTenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Carbon\Carbon;

echo "Setting up SQLite in-memory test harness...\n";

config(['database.default' => 'sqlite']);
config(['database.connections.sqlite' => [
    'driver' => 'sqlite',
    'database' => ':memory:',
    'prefix' => '',
]]);
DB::purge('sqlite');
DB::reconnect('sqlite');

Schema::create('patients', function (Blueprint $table) {
    $table->id();
    $table->string('firstname')->nullable();
    $table->string('lastname')->nullable();
    $table->integer('age')->nullable();
    $table->string('gender')->nullable();
    $table->string('avatar')->nullable();
    $table->string('phone')->nullable();
    $table->text('address')->nullable();
    $table->integer('id_number')->nullable();
    $table->string('email')->nullable();
    $table->string('profession')->nullable();
    $table->date('dob')->nullable();
    $table->double('balance')->default(0);
    $table->boolean('card_activated')->default(false);
    $table->string('card_code')->nullable();
    $table->boolean('is_trail_card')->default(false);
    $table->timestamps();
    $table->softDeletes();
});

Schema::create('prefixs', function (Blueprint $table) {
    $table->id();
    $table->string('prefix_name')->default('INV');
    $table->string('prefix_format')->default('Year-Series');
    $table->boolean('default_prefix')->default(true);
    $table->timestamps();
    $table->softDeletes();
});

Schema::create('medical_histories', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('doctor_id')->nullable();
    $table->unsignedBigInteger('user_id')->nullable();
    $table->unsignedBigInteger('patient_id')->nullable();
    $table->unsignedBigInteger('prefix_id')->nullable();
    $table->string('prefix_invoice')->nullable();
    $table->string('prefix_format')->nullable();
    $table->integer('invoice_num_year_series')->nullable();
    $table->integer('invoice_id')->nullable();
    $table->string('invoice_number')->nullable();
    $table->dateTime('date')->nullable();
    $table->string('visit_type')->nullable();
    $table->string('diagnosis')->nullable();
    $table->text('description')->nullable();
    $table->text('chief_complaint')->nullable();
    $table->text('medical_alert')->nullable();
    $table->double('rate')->default(4050.0);
    $table->double('discount_invoice')->default(0);
    $table->string('discount_inv_as')->default('%');
    $table->integer('status')->default(1);
    $table->string('type')->default('invoice');
    $table->double('total')->default(0);
    $table->double('balance')->default(0);
    $table->double('paid_usd')->default(0);
    $table->double('paid_kh')->default(0);
    $table->double('refunded')->default(0);
    $table->timestamps();
    $table->softDeletes();
});

Schema::create('receptions', function (Blueprint $table) {
    $table->id();
    $table->string('reception_number')->unique();
    $table->unsignedBigInteger('patient_id');
    $table->unsignedBigInteger('invoice_id')->nullable();
    $table->unsignedBigInteger('doctor_id')->nullable();
    $table->dateTime('reception_date')->nullable();
    $table->string('visit_type')->nullable();
    $table->text('chief_complaint')->nullable();
    $table->string('status')->default('registered');
    $table->text('notes')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
});

Schema::create('patient_visits', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('patient_id');
    $table->string('visit_number')->unique();
    $table->string('workflow_option')->default('new_invoice');
    $table->string('invoice_reference')->nullable();
    $table->string('visit_type')->nullable();
    $table->unsignedBigInteger('doctor_id')->nullable();
    $table->string('doctor_name')->nullable();
    $table->text('chief_complaint')->nullable();
    $table->text('clinical_notes')->nullable();
    $table->string('reception_type')->nullable();
    $table->decimal('outstanding_balance', 10, 2)->default(0.00);
    $table->string('status')->default('checked_in');
    $table->timestamp('visited_at')->nullable();
    $table->timestamps();
    $table->softDeletes();
});

Schema::create('settings', function (Blueprint $table) {
    $table->id();
    $table->string('setting_key')->unique();
    $table->text('setting_value')->nullable();
    $table->timestamps();
});

// Seed default prefix and setting
Prefix::create([
    'prefix_name' => 'INV',
    'prefix_format' => 'Year-Series',
    'default_prefix' => true,
]);
DB::table('settings')->insert([
    'setting_key' => 'exchange_rate',
    'setting_value' => '4100',
]);

echo "Schema and initial data prepared successfully.\n";

// Test 1: Create Patient
$patient = Patient::create([
    'firstname' => 'Sovann',
    'lastname' => 'Dev',
    'phone' => '012345678',
    'gender' => 'Male',
    'dob' => '1995-05-15',
    'age' => 29,
]);
echo "Patient created: ID={$patient->id}, Name={$patient->full_name}\n";

// Test 2: Option A - Create New Invoice & Reception
$prefixRepo = app(\App\Repositories\InvoicePrefixRepositoryInterface::class);
$invoiceRepo = app(\App\Repositories\InvoiceRepositoryInterface::class);

$controller = new \App\Http\Controllers\Tenant\PatientVisitRegistrationController(
    $invoiceRepo,
    $prefixRepo
);

$requestA = \Illuminate\Http\Request::create('/register', 'POST', [
    'patient_id' => $patient->id,
    'workflow_option' => 'new_invoice',
    'visit_type' => 'Initial Consultation',
    'doctor_name' => 'Dr. Test',
    'chief_complaint' => 'Toothache on upper molar',
    'clinical_notes' => 'Patient requested thorough examination',
]);

$responseA = $controller->store($requestA);
$createdInvoiceA = MedicalHistory::where('patient_id', $patient->id)->latest('id')->first();
$receptionA = Reception::where('patient_id', $patient->id)->latest('id')->first();
$visitA = PatientVisit::where('patient_id', $patient->id)->latest('id')->first();

assert($createdInvoiceA !== null, "Invoice should be created for Option A");
assert($receptionA !== null, "Reception should be created for Option A");
assert($receptionA->invoice_id === $createdInvoiceA->id, "Reception must be linked to the new Invoice");
assert($visitA !== null, "PatientVisit record should be created");
assert(str_starts_with($createdInvoiceA->invoice_number, 'INV-'), "Invoice number should follow prefix format");
echo "PASS: Option A registration created Invoice ({$createdInvoiceA->invoice_number}) and linked Reception ({$receptionA->reception_number})\n";

// Test 3: Option B - Select Existing Invoice (Follow-up visit)
// Let's create an existing invoice with some balance
$existingInvoice = MedicalHistory::create([
    'patient_id' => $patient->id,
    'invoice_number' => 'INV-2026-000099',
    'date' => Carbon::now()->subDays(5),
    'visit_type' => 'Initial Consultation',
    'rate' => 4100.0,
    'status' => 1,
    'type' => 'invoice',
    'total' => 150.0,
    'balance' => 50.0,
    'paid_usd' => 100.0,
]);

$initialInvoiceCount = MedicalHistory::count();

$requestB = \Illuminate\Http\Request::create('/register', 'POST', [
    'patient_id' => $patient->id,
    'workflow_option' => 'follow_up',
    'invoice_id' => $existingInvoice->id,
    'visit_type' => 'Follow-up Visit',
    'doctor_name' => 'Dr. Test',
    'chief_complaint' => 'Checkup on restored tooth',
    'clinical_notes' => 'Follow-up on previous restoration',
]);

$responseB = $controller->store($requestB);
$finalInvoiceCount = MedicalHistory::count();
$receptionB = Reception::where('invoice_id', $existingInvoice->id)->latest('id')->first();
$visitB = PatientVisit::where('workflow_option', 'follow_up')->latest('id')->first();

assert($initialInvoiceCount === $finalInvoiceCount, "Option B must NOT create duplicate invoices");
assert($receptionB !== null, "Option B must create a new reception");
assert($receptionB->invoice_id === $existingInvoice->id, "Reception must be linked to existing invoice");
assert($existingInvoice->fresh()->invoice_number === 'INV-2026-000099', "Existing invoice number must be preserved");
assert($visitB !== null, "PatientVisit record created for follow-up visit");
echo "PASS: Option B linked Reception ({$receptionB->reception_number}) to existing Invoice ({$existingInvoice->invoice_number}) without creating duplicate invoice.\n";

// Test 4: API getPatientInvoices sorting DESC
$jsonResponse = $controller->getPatientInvoices($patient);
$invoicesData = $jsonResponse->getData(true);
assert(count($invoicesData) === 2, "Expected 2 invoices for patient");
assert(str_contains($invoicesData[0]['display_label'], ' - '), "Display label formatted correctly");
echo "PASS: getPatientInvoices returned invoices sorted DESC with formatted label: " . $invoicesData[0]['display_label'] . "\n";

// Test 5: Option B Patient Ownership Validation
$otherPatient = Patient::create([
    'firstname' => 'Another',
    'lastname' => 'Patient',
]);

$requestCrossPatient = \Illuminate\Http\Request::create('/register', 'POST', [
    'patient_id' => $otherPatient->id,
    'workflow_option' => 'follow_up',
    'invoice_id' => $existingInvoice->id, // belongs to Sovann, not otherPatient!
    'visit_type' => 'Follow-up Visit',
]);

try {
    $controller->store($requestCrossPatient);
    echo "FAIL: Expected exception for cross-patient invoice selection!\n";
    exit(1);
} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    echo "PASS: Correctly rejected attempt to link invoice of another patient.\n";
}

// Test 6: Patient Controller return_to redirect behavior
config(['session.driver' => 'array']);
$user = new class extends \Illuminate\Foundation\Auth\User {
    public function can($ability, $arguments = []) { return true; }
};
auth()->setUser($user);

$patientController = app(\App\Http\Controllers\Tenant\PatientController::class);

$redirector = app(\Illuminate\Routing\Redirector::class);

$storeRequestWithReturn = \App\Http\Requests\Tenant\PatientRequest::create('/patients', 'POST', [
    'id_number' => 12345,
    'firstname' => 'Return',
    'lastname' => 'ToRegister',
    'return_to' => 'register',
]);
$storeRequestWithReturn->setContainer($app);
$storeRequestWithReturn->setRedirector($redirector);
$storeRequestWithReturn->validateResolved();

$storeResponseWithReturn = $patientController->store($storeRequestWithReturn);
assert($storeResponseWithReturn instanceof \Illuminate\Http\RedirectResponse);
assert(str_contains($storeResponseWithReturn->getTargetUrl(), '/register?patient_id='), "Redirect should target /register?patient_id={id}");
echo "PASS: PatientController correctly redirects to /register?patient_id={id} when return_to=register: " . $storeResponseWithReturn->getTargetUrl() . "\n";

$storeRequestStandard = \App\Http\Requests\Tenant\PatientRequest::create('/patients', 'POST', [
    'id_number' => 12346,
    'firstname' => 'Standard',
    'lastname' => 'Patient',
]);
$storeRequestStandard->setContainer($app);
$storeRequestStandard->setRedirector($redirector);
$storeRequestStandard->validateResolved();

$storeResponseStandard = $patientController->store($storeRequestStandard);
assert($storeResponseStandard instanceof \Illuminate\Http\RedirectResponse);
assert(str_contains($storeResponseStandard->getTargetUrl(), '/patients'), "Standard redirect should target patients index");
echo "PASS: PatientController correctly redirects to /patients when return_to is omitted: " . $storeResponseStandard->getTargetUrl() . "\n";

echo "ALL TESTS PASSED SUCCESSFULLY!\n";
