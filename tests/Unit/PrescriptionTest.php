<?php

namespace Tests\Unit;

use App\Http\Requests\Tenant\PrescriptionRequest;
use App\Models\Tenant\Medicine;
use App\Models\Tenant\Patient;
use App\Models\Tenant\Prescription;
use App\Models\Tenant\PrescriptionDetail;
use App\Repositories\PrescriptionRepository;
use App\Repositories\PrescriptionRepositoryInterface;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class PrescriptionTest extends TestCase
{
    public function test_prescription_detail_total_calculation_matches_pdf_and_screenshot(): void
    {
        // Reference case from Screenshot:
        // Dose = 1, Qty = 1, U/D = 1, Morning = true, Evening = true
        // 2 times per day * 1 tablet per time * 1 day = 2 tablets ("2 គ្រាប់")
        $detail = new PrescriptionDetail([
            'dose' => 1,
            'dose_amount' => 1,
            'duration' => 1,
            'unit_dose' => 1,
            'morning' => true,
            'afternoon' => false,
            'evening' => true,
            'night' => false,
        ]);

        $this->assertEquals(2.0, $detail->total_calculated);
        $this->assertEquals('ព្រឹក1 ល្ងាច1', $detail->usage_text);
    }

    public function test_prescription_detail_multi_day_calculation(): void
    {
        // Qty = 2, 3 times a day (Morning, Afternoon, Evening), 5 days
        // Total = 2 * 3 * 5 = 30 tablets
        $detail = new PrescriptionDetail([
            'dose' => 500,
            'dose_amount' => 2,
            'duration' => 5,
            'unit_dose' => 5,
            'morning' => true,
            'afternoon' => true,
            'evening' => true,
            'night' => false,
        ]);

        $this->assertEquals(30.0, $detail->total_calculated);
        $this->assertEquals('ព្រឹក2 ថ្ងៃ2 ល្ងាច2', $detail->usage_text);
    }

    public function test_prescription_model_fillables_and_casts(): void
    {
        $prescription = new Prescription([
            'patient_id' => 123,
            'medical_history_id' => 456,
            'user_id' => 789,
            'prescription' => 'Augmentin 1000mg (2 គ្រាប់)',
        ]);

        $this->assertEquals(123, $prescription->patient_id);
        $this->assertEquals(456, $prescription->medical_history_id);
        $this->assertEquals(789, $prescription->user_id);
        $this->assertEquals('Augmentin 1000mg (2 គ្រាប់)', $prescription->prescription);
    }

    public function test_prescription_request_validation(): void
    {
        $request = new PrescriptionRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('patient_id', $rules);
        $this->assertArrayHasKey('details', $rules);
        $this->assertArrayHasKey('details.*.medicine_id', $rules);
        $this->assertArrayHasKey('details.*.qty', $rules);
        $this->assertArrayHasKey('details.*.ud', $rules);

        // Test with invalid data
        $validator = Validator::make([], $rules);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('patient_id', $validator->errors()->messages());
        $this->assertArrayHasKey('details', $validator->errors()->messages());
    }

    public function test_prescription_routes_are_registered(): void
    {
        $routes = collect(Route::getRoutes()->getRoutes())->map(fn($r) => $r->getName())->filter();

        $this->assertTrue($routes->contains('prescriptions.index'));
        $this->assertTrue($routes->contains('prescriptions.store'));
        $this->assertTrue($routes->contains('prescriptions.update'));
        $this->assertTrue($routes->contains('prescriptions.destroy'));
        $this->assertTrue($routes->contains('prescriptions.print'));
        $this->assertTrue($routes->contains('prescriptions.patient-histories'));
    }

    public function test_prescription_repository_interface_binding(): void
    {
        $repo = app(PrescriptionRepositoryInterface::class);
        $this->assertInstanceOf(PrescriptionRepository::class, $repo);
    }
}
