<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Repositories\CompanyRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class CreateTenantCompany implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Tenant $tenant)
    {
        $this->company = App(CompanyRepository::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $this->tenant->run(function ($tenant){
            $datas = [];
            $datas['name_international'] = $tenant->name_international ?? $tenant->company ?? $tenant->name ?? 'Company';
            $datas['name_native'] = $tenant->name_native ?? $tenant->name_international ?? $tenant->company ?? $tenant->name ?? 'Company';
            $datas['phone'] = $tenant->phone;
            $datas['range'] = $tenant->range ?? null;
            $datas['latlng'] = !empty($tenant->latlng) ? (is_string($tenant->latlng) ? $tenant->latlng : json_encode($tenant->latlng)) : null;
            if (\Illuminate\Support\Facades\Schema::hasColumn('companies', 'locked_division')) {
                $datas['locked_division'] = false;
            }
            $company = $this->company->create($datas);
        });
    }
}
