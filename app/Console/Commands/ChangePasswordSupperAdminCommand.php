<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;

class ChangePasswordSupperAdminCommand extends Command
{
    protected $signature = 'py:change-password-super-admin {--new=}';
    // php artisan py:change-password --new=12345678

    protected $description = 'Change password super admin to central. {--new=}';

    public function __construct()
    {
        parent::__construct();
        $this->user = App(UserRepository::class);
    }

    public function handle()
    {
        $newPassword = $this->option('new');
        if(!empty($newPassword)){
            $this->info('Change password super admin to central');
            $data = [
                'password'=> Hash::make($newPassword)
            ];

            $user = app(User::class)->query()->where('name','Super Admin')->where('email','superadmin@pethyoeung.com')->where('level_access',User::SUPER_ADMIN)->get();
            if(count($user)){
                $this->user->update($user[0],$data);
                $this->info('Password changed successfully');
                return Command::SUCCESS;
            }
            $this->info('Super admin not found');
            return Command::FAILURE;
        }else{
            $this->info('Failed to update password super admin');
            $this->info('Please input new password for change password super admin Ex --new=newPassword');
            return Command::FAILURE;
        }

        
    }
}
