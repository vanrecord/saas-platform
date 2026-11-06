<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\Tenant\DentalChart;
use App\Models\Tenant\DentalCondition;
use App\Models\Tenant\Patient;
use App\Services\Tenant\DentalChartSyncService;
use Tests\TestCase;

class DentalChartModuleTest extends TestCase
{
    protected Tenant $tenant1;
    protected Tenant $tenant2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant1 = Tenant::firstOrCreate(
            ['id' => '06fbf6a8-25a1-4139-a3d0-fc8b3c4ecd60'],
            [
                'company' => 'dental',
                'domain' => 'dental',
                'name' => 'Dr. Tenant One',
                'email' => 't1@dental.test',
                'password' => bcrypt('password'),
            ]
        );

        $this->tenant2 = Tenant::firstOrCreate(
            ['id' => '11111111-2222-3333-4444-555555555555'],
            [
                'company' => 'clinic2',
                'domain' => 'clinic2',
                'name' => 'Dr. Tenant Two',
                'email' => 't2@clinic.test',
                'password' => bcrypt('password'),
            ]
        );
    }

    protected function tearDown(): void
    {
        if (tenancy()->initialized) {
            tenancy()->end();
        }
        parent::tearDown();
    }

    public function test_tenant_data_isolation_between_two_tenants(): void
    {
        $uniqueNumA = 'P-A-' . uniqid();
        $uniqueNumB = 'P-B-' . uniqid();

        // --- 1. Tenant 1 operations ---
        tenancy()->initialize($this->tenant1);

        $patientA = Patient::create([
            'patient_number' => $uniqueNumA,
            'full_name' => 'Alice Smith',
            'sex' => 'female',
            'phone' => '012999001',
        ]);

        $chartA = DentalChart::create([
            'patient_id' => $patientA->id,
            'chart_date' => '2026-11-05',
            'dentition_type' => 'mixed',
            'examination_notes' => 'Patient A initial examination',
        ]);

        $syncService = app(DentalChartSyncService::class);
        $syncService->sync($chartA, [
            'dentition_type' => 'mixed',
            'examination_notes' => 'Patient A examination notes updated',
            'findings' => [
                [
                    'tooth' => 23,
                    'surface' => 'top',
                    'condition_code' => 'caries',
                    'color' => '#d83c43',
                    'status' => 'observed',
                    'note' => 'Occlusal caries detected',
                ],
                [
                    'tooth' => 16,
                    'surface' => 'whole',
                    'condition_code' => 'crown',
                    'color' => '#8b5bc7',
                    'status' => 'completed',
                    'note' => 'Existing porcelain crown',
                ],
            ],
        ]);

        $this->assertEquals(1, Patient::where('patient_number', $uniqueNumA)->count());
        $this->assertEquals(2, $chartA->teeth()->count());
        $this->assertEquals(2, $chartA->findings()->count());

        tenancy()->end();

        // --- 2. Tenant 2 operations & isolation verification ---
        tenancy()->initialize($this->tenant2);

        // Tenant 2 must NOT see Tenant 1's records
        $this->assertNull(Patient::where('patient_number', $uniqueNumA)->first());

        // Create Tenant 2 patient and chart
        $patientB = Patient::create([
            'patient_number' => $uniqueNumB,
            'full_name' => 'Bob Jones',
            'sex' => 'male',
            'phone' => '012999002',
        ]);

        $chartB = DentalChart::create([
            'patient_id' => $patientB->id,
            'chart_date' => '2026-11-05',
            'dentition_type' => 'permanent',
            'examination_notes' => 'Patient B clinical notes',
        ]);

        $syncService->sync($chartB, [
            'dentition_type' => 'permanent',
            'examination_notes' => 'Patient B clinical notes updated',
            'findings' => [
                [
                    'tooth' => 11,
                    'surface' => 'center',
                    'condition_code' => 'restoration',
                    'color' => '#2f66cf',
                    'status' => 'planned',
                    'note' => 'Composite restoration planned',
                ],
            ],
        ]);

        $this->assertEquals(1, Patient::where('patient_number', $uniqueNumB)->count());
        $this->assertEquals(1, $chartB->teeth()->count());
        $this->assertEquals(1, $chartB->findings()->count());

        // Confirm Tenant 2 still does not see Tenant 1's patient
        $this->assertNull(Patient::where('patient_number', $uniqueNumA)->first());

        tenancy()->end();

        // --- 3. Switch back to Tenant 1 to verify it does NOT see Tenant 2's data ---
        tenancy()->initialize($this->tenant1);

        $this->assertNull(Patient::where('patient_number', $uniqueNumB)->first());
        $this->assertNotNull(Patient::where('patient_number', $uniqueNumA)->first());
        // Verify Chart in Tenant 1 belongs to Patient A, never Patient B
        $tenant1Chart = DentalChart::find($chartA->id);
        $this->assertEquals($patientA->id, $tenant1Chart->patient_id);
        $this->assertEquals('Alice Smith', $tenant1Chart->patient->full_name);

        tenancy()->end();
    }

    public function test_sync_service_upsert_and_removal_workflow(): void
    {
        tenancy()->initialize($this->tenant1);

        $patient = Patient::create([
            'patient_number' => 'P-SYNC-' . uniqid(),
            'full_name' => 'Sync Test Patient',
        ]);

        $chart = DentalChart::create([
            'patient_id' => $patient->id,
            'chart_date' => '2026-11-05',
            'dentition_type' => 'mixed',
        ]);

        $syncService = app(DentalChartSyncService::class);

        // Step 1: Initial sync with 2 findings on tooth 23 (top and left)
        $syncService->sync($chart, [
            'findings' => [
                ['tooth' => 23, 'surface' => 'top', 'condition_code' => 'caries', 'color' => '#d83c43'],
                ['tooth' => 23, 'surface' => 'left', 'condition_code' => 'caries', 'color' => '#d83c43'],
            ],
        ]);

        $tooth23 = $chart->teeth()->where('tooth_number', 23)->first();
        $this->assertNotNull($tooth23);
        $this->assertEquals(2, $tooth23->findings()->count());

        // Step 2: Remove 'left' surface finding, keep 'top'
        $syncService->sync($chart, [
            'findings' => [
                ['tooth' => 23, 'surface' => 'top', 'condition_code' => 'caries', 'color' => '#d83c43'],
            ],
        ]);

        $this->assertEquals(1, $tooth23->fresh()->findings()->count());
        $this->assertEquals('top', $tooth23->fresh()->findings()->first()->surface);

        // Step 3: Clear all findings for tooth 23
        $syncService->sync($chart, [
            'findings' => [],
        ]);

        $this->assertEquals(0, $tooth23->fresh()->findings()->count());

        tenancy()->end();
    }

    public function test_dental_conditions_crud_operations(): void
    {
        tenancy()->initialize($this->tenant1);

        // Verify seeded conditions exist
        $this->assertGreaterThanOrEqual(8, DentalCondition::count());

        // Create new condition
        $custom = DentalCondition::create([
            'name' => 'Impacted Wisdom Tooth',
            'code' => 'impacted',
            'default_color' => '#9333ea',
            'category' => 'finding',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('dental_conditions', ['code' => 'impacted']);

        // Update
        $custom->update(['default_color' => '#7e22ce']);
        $this->assertEquals('#7e22ce', $custom->fresh()->default_color);

        // Delete
        $custom->delete();
        $this->assertDatabaseMissing('dental_conditions', ['code' => 'impacted']);

        tenancy()->end();
    }

    public function test_tenant_api_sync_findings_endpoint(): void
    {
        tenancy()->initialize($this->tenant1);
        $user = \App\Models\UserTenant::first();
        $patient = Patient::create([
            'patient_number' => 'P-API-' . uniqid(),
            'full_name' => 'API Test Patient',
        ]);
        $chart = DentalChart::create([
            'patient_id' => $patient->id,
            'chart_date' => '2026-11-05',
        ]);
        tenancy()->end();

        $response = $this->actingAs($user, 'web')
            ->postJson("http://dental.tenancy-core.test/api/dental-charts/{$chart->id}/sync-findings", [
                'dentition_type' => 'mixed',
                'examination_notes' => 'API sync test notes',
                'findings' => [
                    [
                        'tooth' => 23,
                        'surface' => 'top',
                        'condition_code' => 'caries',
                        'color' => '#d83c43',
                        'status' => 'observed',
                        'note' => 'Caries on occlusal surface',
                    ],
                ],
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $chart->id);
        $response->assertJsonPath('data.examination_notes', 'API sync test notes');
    }

    public function test_tenant_web_pages_render_for_authenticated_tenant_user(): void
    {
        tenancy()->initialize($this->tenant1);
        $user = \App\Models\UserTenant::first();
        $patient = Patient::create([
            'patient_number' => 'P-WEB-' . uniqid(),
            'full_name' => 'Web Patient',
        ]);
        $chart = DentalChart::create([
            'patient_id' => $patient->id,
            'chart_date' => '2026-11-05',
        ]);
        tenancy()->end();

        // Interactive chart page
        $this->actingAs($user, 'web')
            ->get("http://dental.tenancy-core.test/dental/{$chart->id}")
            ->assertStatus(200);

        // Dental charts list
        $this->actingAs($user, 'web')
            ->get('http://dental.tenancy-core.test/dental-charts')
            ->assertStatus(200);

        // Patients list
        $this->actingAs($user, 'web')
            ->get('http://dental.tenancy-core.test/patients')
            ->assertStatus(200);

        // Dental conditions list
        $this->actingAs($user, 'web')
            ->get('http://dental.tenancy-core.test/dental-conditions')
            ->assertStatus(200);
    }

    public function test_tenant_root_and_login_url_render_login_form_for_guest(): void
    {
        // Visiting tenant root URL as a guest renders tenant login form
        $this->get('http://dental.tenancy-core.test/')
            ->assertStatus(200)
            ->assertInertia(fn ($page) => $page
                ->component('Auth/Login')
                ->where('tenant', true)
            );

        // Visiting tenant /login directly as a guest renders tenant login form
        $this->get('http://dental.tenancy-core.test/login')
            ->assertStatus(200)
            ->assertInertia(fn ($page) => $page
                ->component('Auth/Login')
                ->where('tenant', true)
            );
    }

    public function test_tenant_login_authenticates_and_redirects_to_dental(): void
    {
        $response = $this->post('http://dental.tenancy-core.test/login', [
            'email' => 'sovann@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }
}
