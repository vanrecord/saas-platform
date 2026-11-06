<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Domain;
use App\Models\UserTenant;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use App\Repositories\ProvinceRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CreatePersonalAccessToken implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Domain $domain)
    {
        $this->province = App(ProvinceRepository::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $province_code = $this->province->find($this->domain->tenant->province_id)?->code;
        $this->domain->tenant->run(function ($tenant) use ($province_code) {
            try {
                app(\Stancl\Tenancy\Resolvers\DomainTenantResolver::class)->invalidateCache($tenant);
                // create token for tenant
                $token = Str::random(40);
                $data = [
                        'name'           => "BMS Token",
                        'token'          => hash('sha256', $token),
                        'abilities'      => '["create","update","read"]',
                        'tokenable_id'   => UserTenant::first()->id,
                        'tokenable_type' => User::class
                    ];
                
                \DB::table("personal_access_tokens")->insert($data);
    
                $datas = [
                    'english_name'  => $tenant->company,
                    'local_name'    => $tenant->company,
                    'domain'        => \Request::getScheme().'://'.optional($tenant->domains->first())->domain,
                    'config_token'  => json_encode(['token' => 'Bearer '.$token]),
                    'lat'           => is_array($tenant->latlng) ? ($tenant->latlng['lat'] ?? null) : null,
                    'lng'           => is_array($tenant->latlng) ? ($tenant->latlng['lng'] ?? null) : null,
                    'phone'         => $tenant->phone,
                    'province_code' => $province_code,
                    'email'         => null,
                    'address'       => null,
                    'country_code'  => null,
                    'logo'          => null
                ];
                // end create token for tenant
                
                // generate token for e-Health
                $datas = aes_encryption(json_encode($datas));
                $res = Http::withHeaders(config('ehealth.header'))
                            ->withToken(config('ehealth.token'))
                            ->post(config('ehealth.ehealth_api_url').'api/bms/v1/organisation/create',$datas);
                $datas = json_decode($res,true);
                // end generate token for e-Health
                // code generate from e-Health
                if(!empty($datas['code']) && $datas['code'] == 100 && !empty($datas['data'])) {
                    // $tenant->domains[0]->update([
                    //     'domain'=> $tenant->domain,
                    //     'ehealth_organization_code' => $datas['data']['code']
                    // ]);
                    Domain::withoutEvents(function() use ($tenant,$datas){   
                        $tenant->domains[0]->update([
                            'ehealth_organization_code' => $datas['data']['code']
                        ]);
                    });
                }
            }catch (\Throwable $th) {
                return ;
            }
        });
    }
}
