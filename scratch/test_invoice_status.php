<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant\MedicalHistory;

echo "Testing MedicalHistory status constants & determineStatus...\n";

assert(MedicalHistory::PARTIAL === 1, "PARTIAL must be 1");
assert(MedicalHistory::UNPAID === 2, "UNPAID must be 2");
assert(MedicalHistory::PAID === 3, "PAID must be 3");
assert(MedicalHistory::REFUNED === 4, "REFUNED must be 4");
assert(MedicalHistory::PARTIALLY_REFUNED === 5, "PARTIALLY_REFUNED must be 5");

echo "Constants verified:\n";
echo "  PARTIAL = " . MedicalHistory::PARTIAL . "\n";
echo "  UNPAID = " . MedicalHistory::UNPAID . "\n";
echo "  PAID = " . MedicalHistory::PAID . "\n";
echo "  REFUNED = " . MedicalHistory::REFUNED . "\n";
echo "  PARTIALLY_REFUNED = " . MedicalHistory::PARTIALLY_REFUNED . "\n";

// Test 1: New Invoice with total > 0, paid = 0, balance = total
$s1 = MedicalHistory::determineStatus(100.0, 0.0, 0.0, 100.0);
assert($s1 === MedicalHistory::UNPAID, "Expected UNPAID (2), got {$s1}");
assert(MedicalHistory::statusLabel($s1) === 'Unpaid');
echo "PASS: Total $100, Paid $0 => UNPAID (2)\n";

// Test 2: Partial payment: total 100, paid 40, balance 60
$s2 = MedicalHistory::determineStatus(100.0, 40.0, 0.0, 60.0);
assert($s2 === MedicalHistory::PARTIAL, "Expected PARTIAL (1), got {$s2}");
assert(MedicalHistory::statusLabel($s2) === 'Partial');
echo "PASS: Total $100, Paid $40, Balance $60 => PARTIAL (1)\n";

// Test 3: Paid in full: total 100, paid 100, balance 0
$s3 = MedicalHistory::determineStatus(100.0, 100.0, 0.0, 0.0);
assert($s3 === MedicalHistory::PAID, "Expected PAID (3), got {$s3}");
assert(MedicalHistory::statusLabel($s3) === 'Paid');
echo "PASS: Total $100, Paid $100, Balance $0 => PAID (3)\n";

// Test 4: Fully Refunded: total 100, paid 100, refunded 100, balance 100
$s4 = MedicalHistory::determineStatus(100.0, 100.0, 100.0, 100.0);
assert($s4 === MedicalHistory::REFUNED, "Expected REFUNED (4), got {$s4}");
assert(MedicalHistory::statusLabel($s4) === 'Refunded');
echo "PASS: Paid $100, Refunded $100 => REFUNED (4)\n";

// Test 5: Partially Refunded: total 100, paid 100, refunded 30, balance 30
$s5 = MedicalHistory::determineStatus(100.0, 100.0, 30.0, 30.0);
assert($s5 === MedicalHistory::PARTIALLY_REFUNED, "Expected PARTIALLY_REFUNED (5), got {$s5}");
assert(MedicalHistory::statusLabel($s5) === 'Partially Refunded');
echo "PASS: Paid $100, Refunded $30 => PARTIALLY_REFUNED (5)\n";

// Test 6: Model status_label attribute
$invoice = new MedicalHistory(['status' => MedicalHistory::PARTIAL]);
assert($invoice->status_label === 'Partial');

$invoice->status = MedicalHistory::UNPAID;
assert($invoice->status_label === 'Unpaid');

$invoice->status = MedicalHistory::PAID;
assert($invoice->status_label === 'Paid');

$invoice->status = MedicalHistory::REFUNED;
assert($invoice->status_label === 'Refunded');

$invoice->status = MedicalHistory::PARTIALLY_REFUNED;
assert($invoice->status_label === 'Partially Refunded');

echo "PASS: Model status_label accessor verified for all 5 statuses.\n";
echo "ALL STATUS TESTS COMPLETED SUCCESSFULLY!\n";
