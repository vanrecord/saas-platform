<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserTenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Concerns\HasATenantsOption;
use Stancl\Tenancy\Concerns\ExtendsLaravelCommand;
use Stancl\Tenancy\Concerns\TenantAwareCommand;



class CreateUserDevTenant extends Command
{
    use HasATenantsOption, ExtendsLaravelCommand;
    protected $description = 'Seed user dev to tenant';
   
    protected static function getTenantCommandName(): string
    {
        return 'py:seed-user-dev-tenant';
    }
  
    public function handle()
    {

        tenancy()->runForMultiple($this->option('tenants'), function ($tenant) {
            $this->line("Tenant: {$tenant->getTenantKey()}");
            $user = UserTenant::where('email','dev@pethyoeung.com')
                                ->where('level_access',UserTenant::USER_DEV)
                                ->get();
            UserTenant::withoutEvents(function() use ($tenant, &$user){   
                if(count($user)== 0){
                    $existing = UserTenant::create([
                        'name'         => 'User dev',
                        'email'        => 'dev@pethyoeung.com',
                        'password'     => Hash::make('MxRYav7NM8S8'),
                        'level_access' => UserTenant::USER_DEV,
                        'email_verified_at' => date('Y-m-d H:i:s'),
                        'active'    => true
                    ]);
                }
            });
        });
    }
    
}
