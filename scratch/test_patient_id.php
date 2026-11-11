<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant\Patient;
use App\Repositories\PatientRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

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
    $table->integer('id_number')->nullable();
    $table->timestamps();
    $table->softDeletes();
});

$repo = app(PatientRepository::class);

// Test 1: Empty database
$latest = $repo->getLatestId();
assert($latest === null, "Should be null when empty");
$next = $repo->getNextIdNumber();
assert($next === 1, "Should be 1 when empty, got {$next}");
echo "PASS: Empty database returns null latest, next is 1\n";

// Test 2: Add patients
Patient::create(['firstname' => 'Alice', 'id_number' => 10]);
Patient::create(['firstname' => 'Bob', 'id_number' => 25]);
Patient::create(['firstname' => 'Charlie', 'id_number' => 18]);

$latest = $repo->getLatestId();
assert($latest !== null && $latest->id_number === 25, "Latest id_number should be 25");
$next = $repo->getNextIdNumber();
assert($next === 26, "Next id_number should be 26, got {$next}");
echo "PASS: With patients (10, 25, 18), latest is 25, next is 26\n";

// Test 3: Soft delete Bob (id_number = 25)
$latest->delete();
$latestWithTrash = $repo->getLatestId();
assert($latestWithTrash !== null && $latestWithTrash->id_number === 25, "withTrashed should still find deleted id 25");
$nextWithTrash = $repo->getNextIdNumber();
assert($nextWithTrash === 26, "Next should still be 26 even when 25 was deleted");
echo "PASS: withTrashed() properly preserves sequence even when latest is soft-deleted\n";

echo "ALL PATIENT ID TESTS PASSED!\n";
