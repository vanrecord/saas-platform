<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserTenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Concerns\HasATenantsOption;
use Stancl\Tenancy\Concerns\ExtendsLaravelCommand;
use Stancl\Tenancy\Concerns\TenantAwareCommand;

class ChangePasswordUserDevCommand extends Command
{
    use HasATenantsOption;
    protected $signature = 'py:change-password-user-dev {--new=}';

    protected $description = 'Change password user dev to all tenants. {--new=}';

    public function handle()
    {
        $newPassword = $this->option('new');
        if($newPassword){
            $this->info('Change password user dev');
            tenancy()->runForMultiple($this->option('tenants'), function ($tenant) use ($newPassword){
                $this->line("Tenant: {$tenant->getTenantKey()}");
                $user = UserTenant::where('email','dev@pethyoeung.com')
                                    ->where('level_access',UserTenant::USER_DEV)
                                    ->get();
                UserTenant::withoutEvents(function() use ($tenant, &$user,&$newPassword){   
                    if(count($user)){
                        $user[0]->update(['password'=> Hash::make($newPassword)]);
                    }
                });
            });
            $this->info('Password changed successfully');
            return Command::SUCCESS;
        }else{
            $this->info('Failed to update password user dev');
            $this->info('Please input new password for change password user dev Ex --new=newPassword');
            return Command::FAILURE;
        }

        
    }
}
