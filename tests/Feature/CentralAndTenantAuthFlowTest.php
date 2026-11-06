<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserTenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CentralAndTenantAuthFlowTest extends TestCase
{
    protected User $centralUser;
    protected Tenant $dentalTenant;
    protected Tenant $hospitalTenant;
    protected Domain $dentalDomain;
    protected Domain $hospitalDomain;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Central Admin User in saas_central
        $this->centralUser = User::updateOrCreate(
            ['email' => 'sovann@gmail.com'],
            [
                'name' => 'Sovann Central Admin',
                'password' => Hash::make('12345678'),
                'is_active' => true,
                'level_access' => 2,
                'email_verified_at' => now(),
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
                'user_id' => $this->centralUser->id,
                'site_number' => 'DENT-001',
                'created_by' => $this->centralUser->id,
                'updated_by' => $this->centralUser->id,
            ]
        );

        // Seed Dental tenant user in tenant DB
        tenancy()->initialize($this->dentalTenant);
        UserTenant::withoutEvents(function () {
            UserTenant::updateOrCreate(
                ['email' => 'sovann@gmail.com'],
                [
                    'name' => 'Dr. Sovann Dental',
                    'password' => Hash::make('12345678'),
                    'is_active' => true,
                    'level_access' => 1,
                ]
            );
        });
        tenancy()->end();

        // 3. Hospital Tenant & Domain
        $this->hospitalTenant = Tenant::updateOrCreate(
            ['id' => '11111111-2222-3333-4444-555555555555'],
            [
                'data' => [
                    'company' => 'Hospital',
                    'name' => 'Dr. Hospital',
                    'email' => 'hospital@tenancy-core.test',
                    'tenancy_db_name' => 'tenant11111111-2222-3333-4444-555555555555',
                ]
            ]
        );

        $this->hospitalDomain = Domain::updateOrCreate(
            ['tenant_id' => $this->hospitalTenant->id],
            [
                'domain' => 'hospital.tenancy-core.test',
                'company' => 'Hospital',
                'is_active' => true,
                'user_id' => $this->centralUser->id,
                'site_number' => 'HOSP-001',
                'created_by' => $this->centralUser->id,
                'updated_by' => $this->centralUser->id,
            ]
        );

        // Seed Hospital tenant user in tenant DB
        tenancy()->initialize($this->hospitalTenant);
        UserTenant::withoutEvents(function () {
            UserTenant::updateOrCreate(
                ['email' => 'doctor@hospital.test'],
                [
                    'name' => 'Dr. Hospital',
                    'password' => Hash::make('12345678'),
                    'is_active' => true,
                    'level_access' => 1,
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

    /**
     * Test 1: Central Login
     */
    public function test_1_central_login_flow(): void
    {
        // 1. Central login screen renders on saas-platform.test
        $response = $this->get('http://saas-platform.test/login');
        $response->assertStatus(200);

        // 2. Authenticate using Central credentials
        $response = $this->post('http://saas-platform.test/login', [
            'email' => 'sovann@gmail.com',
            'password' => '12345678',
        ]);

        $this->assertAuthenticated('web');
        $this->assertFalse(tenancy()->initialized);
        $response->assertRedirect('/admin');
    }

    /**
     * Test 2: Central Domain Management and Go To button
     */
    public function test_2_central_domain_management_and_go_to(): void
    {
        // Act as Central admin on Central domain
        $response = $this->actingAs($this->centralUser, 'web')
            ->get('http://saas-platform.test/domain');

        $response->assertStatus(200);

        // Verify domains are returned in DataTable props
        $response->assertInertia(function ($page) {
            $page->component('Domain/Index')
                ->has('domains.data', 2)
                ->where('domains.data.0.company', 'Dental Clinic')
                ->where('domains.data.0.domain', 'dental.tenancy-core.test')
                ->where('domains.data.1.company', 'Hospital')
                ->where('domains.data.1.domain', 'hospital.tenancy-core.test');
        });
    }

    /**
     * Test 3: Dental Tenant Login
     */
    public function test_3_dental_tenant_login_flow(): void
    {
        // 1. Visit dental login page
        $response = $this->get('http://dental.tenancy-core.test/login');
        $response->assertStatus(200);

        // 2. Post valid tenant login
        $response = $this->post('http://dental.tenancy-core.test/login', [
            'email' => 'sovann@gmail.com',
            'password' => '12345678',
        ]);

        $this->assertAuthenticated('tenant');
        $this->assertTrue(tenancy()->initialized);
        $this->assertEquals($this->dentalTenant->id, tenant('id'));
        $this->assertEquals('tenant06fbf6a8-25a1-4139-a3d0-fc8b3c4ecd60', DB::connection()->getDatabaseName());
        $response->assertRedirect('/dashboard');
    }

    /**
     * Test 4: Hospital Tenant Login
     */
    public function test_4_hospital_tenant_login_flow(): void
    {
        // 1. Visit hospital login page
        $response = $this->get('http://hospital.tenancy-core.test/login');
        $response->assertStatus(200);

        // 2. Post valid hospital tenant login
        $response = $this->post('http://hospital.tenancy-core.test/login', [
            'email' => 'doctor@hospital.test',
            'password' => '12345678',
        ]);

        $this->assertAuthenticated('tenant');
        $this->assertTrue(tenancy()->initialized);
        $this->assertEquals($this->hospitalTenant->id, tenant('id'));
        $this->assertEquals('tenant11111111-2222-3333-4444-555555555555', DB::connection()->getDatabaseName());
        $response->assertRedirect('/dashboard');
    }

    /**
     * Test 5: Authentication Isolation (cross-system auth fails)
     */
    public function test_5_authentication_isolation(): void
    {
        // 5A: Hospital user cannot log into Central
        $response = $this->post('http://saas-platform.test/login', [
            'email' => 'doctor@hospital.test',
            'password' => '12345678',
        ]);
        $response->assertSessionHasErrors('email');
        $this->assertGuest('web');

        // 5B: Hospital user cannot log into Dental
        $response = $this->post('http://dental.tenancy-core.test/login', [
            'email' => 'doctor@hospital.test',
            'password' => '12345678',
        ]);
        $response->assertSessionHasErrors('email');
        $this->assertGuest('tenant');

        // 5C: Tenant user cannot access Central administration route
        $response = $this->get('http://dental.tenancy-core.test/admin');
        // PreventAccessFromTenantDomains ensures 404 on central routes
        $response->assertStatus(404);

        // 5D: Central domain cannot access tenant routes
        $response = $this->get('http://saas-platform.test/dental');
        $response->assertStatus(404);
    }

    /**
     * Test 6: Tenant Data Isolation
     */
    public function test_6_tenant_data_isolation(): void
    {
        // Connect to Dental and create a patient
        tenancy()->initialize($this->dentalTenant);
        $dentalPatient = \App\Models\Tenant\Patient::updateOrCreate(
            ['patient_number' => 'DENT-PAT-001'],
            ['full_name' => 'Dental Patient Alpha']
        );
        tenancy()->end();

        // Connect to Hospital and check that Dental patient does NOT exist
        tenancy()->initialize($this->hospitalTenant);
        $patientInHospital = \App\Models\Tenant\Patient::where('patient_number', 'DENT-PAT-001')->first();
        $this->assertNull($patientInHospital);

        // Create a hospital-only patient
        $hospitalPatient = \App\Models\Tenant\Patient::updateOrCreate(
            ['patient_number' => 'HOSP-PAT-001'],
            ['full_name' => 'Hospital Patient Beta']
        );
        tenancy()->end();

        // Verify patient isolation back in Dental
        tenancy()->initialize($this->dentalTenant);
        $patientInDental = \App\Models\Tenant\Patient::where('patient_number', 'HOSP-PAT-001')->first();
        $this->assertNull($patientInDental);
        $this->assertNotNull(\App\Models\Tenant\Patient::where('patient_number', 'DENT-PAT-001')->first());
        tenancy()->end();
    }
}

