<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\Tenant;
use App\Models\Tenant\Patient;
use App\Models\Tenant\PatientVisit;
use App\Models\User;
use App\Models\UserTenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TenantVisitRegistrationTest extends TestCase
{
    protected Tenant $dentalTenant;
    protected Domain $dentalDomain;
    protected UserTenant $tenantUser;
    protected Patient $patient;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Central Admin User in saas_central (fallback if needed)
        $centralUser = User::updateOrCreate(
            ['email' => 'admin@central.test'],
            [
                'name' => 'Central Admin',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );

        // 2. Dental Tenant & Domain
        $this->dentalTenant = Tenant::updateOrCreate(
            ['id' => '06fbf6a8-25a1-4139-a3d0-fc8b3c4ecd60'],
            [
                'data' => [
                    'company' => 'Dental Clinic',
                    'name' => 'Dr. Dental',
                    'email' => 'dental@tenancy-core.test',
                    'tenancy_db_name' => 'tenant06fbf6a8-25a1-4139-a3d0-fc8b3c4ecd60',
                ]
            ]
        );

        $this->dentalDomain = Domain::updateOrCreate(
            ['tenant_id' => $this->dentalTenant->id],
            [
                'domain' => 'dental.tenancy-core.test',
                'company' => 'Dental Clinic',
                'is_active' => true,
                'user_id' => $centralUser->id,
            ]
        );

        // 3. Seed user & patient in tenant database
        tenancy()->initialize($this->dentalTenant);

        $this->tenantUser = UserTenant::withoutEvents(function () {
            return UserTenant::updateOrCreate(
                ['email' => 'sovann@gmail.com'],
                [
                    'name' => 'Dr. Sovann Dental',
                    'password' => Hash::make('password123'),
                    'is_active' => true,
                ]
            );
        });

        $this->patient = Patient::withoutEvents(function () {
            return Patient::updateOrCreate(
                ['id_number' => 5001],
                [
                    'firstname' => 'Chan',
                    'lastname' => 'Sophea',
                    'phone' => '012345678',
                    'gender' => 'Female',
                    'dob' => '1995-05-15',
                ]
            );
        });

        tenancy()->end();
    }

    protected function tearDown(): void
    {
        if (tenancy()->initialized) {
            tenancy()->end();
        }
        parent::tearDown();
    }

    public function test_tenant_dashboard_renders_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->tenantUser, 'tenant')
            ->get('http://dental.tenancy-core.test/dashboard');

        $response->assertStatus(200);
    }

    public function test_patient_visit_registration_page_renders(): void
    {
        $response = $this->actingAs($this->tenantUser, 'tenant')
            ->get('http://dental.tenancy-core.test/register');

        $response->assertStatus(200);
    }

    public function test_patient_search_api_returns_results(): void
    {
        $response = $this->actingAs($this->tenantUser, 'tenant')
            ->getJson('http://dental.tenancy-core.test/api/patients/search?q=Sophea');

        $response->assertStatus(200)
            ->assertJsonFragment(['full_name' => 'Chan Sophea']);
    }

    public function test_quick_create_patient_api(): void
    {
        $response = $this->actingAs($this->tenantUser, 'tenant')
            ->postJson('http://dental.tenancy-core.test/api/patients/quick-create', [
                'firstname' => 'John',
                'lastname' => 'Quick Test',
                'phone' => '098765432',
                'gender' => 'Male',
                'dob' => '2000-01-01',
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['firstname' => 'John']);
    }

    public function test_stopped_domain_returns_503(): void
    {
        $this->dentalTenant->putDownForMaintenance();
        Domain::where('tenant_id', $this->dentalTenant->id)->update(['is_active' => false]);

        $response = $this->get('http://dental.tenancy-core.test/login');
        $response->assertStatus(503);

        // Restore
        $this->dentalTenant->update(['maintenance_mode' => null]);
        Domain::where('tenant_id', $this->dentalTenant->id)->update(['is_active' => true]);
    }

    public function test_visit_registration_stores_record(): void
    {
        $response = $this->actingAs($this->tenantUser, 'tenant')
            ->post('http://dental.tenancy-core.test/register', [
                'patient_id' => $this->patient->id,
                'workflow_option' => 'new_invoice',
                'visit_type' => 'Initial Consultation',
                'doctor_id' => $this->tenantUser->id,
                'doctor_name' => $this->tenantUser->name,
                'chief_complaint' => 'Tooth sensitivity on lower left molar',
                'clinical_notes' => 'Patient requested general cleaning and exam.',
                'outstanding_balance' => 0.00,
            ]);

        $response->assertRedirect('http://dental.tenancy-core.test/register');
        $response->assertSessionHas('success');
    }
}

