<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Models\Hospital;
use App\Models\UserTenant;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CreateTenantHospital implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param Tenant $tenant
     */
    public function __construct(public Tenant $tenant)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->tenant->run(function ($tenant) {
            // Retrieve the first user created inside the tenant database
            $firstUser = UserTenant::orderBy('id', 'asc')->first();
            if (! $firstUser && ! empty($tenant->email)) {
                $firstUser = UserTenant::where('email', $tenant->email)->first();
            }

            if (! $firstUser) {
                info("CreateTenantHospital: No tenant user found in tenant database for tenant {$tenant->id}");
                return;
            }

            // Determine the hospital name from the submitted domain/company/tenant information
            $hospitalName = $tenant->company
                ?? $tenant->name_international
                ?? $tenant->name_native
                ?? $tenant->name
                ?? 'Hospital';

            // Create exactly one Hospital record in the tenant database
            Hospital::firstOrCreate(
                ['user_id' => $firstUser->id],
                ['name' => $hospitalName]
            );
        });
    }
}
