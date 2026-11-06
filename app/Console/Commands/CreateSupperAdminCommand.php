<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;

class CreateSupperAdminCommand extends Command
{
    protected $signature = 'py:create-super-admin';

    protected $description = 'Seed super admin to central';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Seeding super admin to central');
        $data = [
            'name'         => 'Super Admin',
            'email'        => 'superadmin@pethyoeung.com',
            'password'     => Hash::make('hmJ$h841uYWv'),
            'level_access' => User::SUPER_ADMIN,
            'email_verified_at' => date('Y-m-d H:i:s'),
            'is_active'    => true
        ];

        $existing = User::firstOrNew(['email'=> $data['email']], $data);
        
        $this->line('');
        if ($existing->id) {
            $this->info('There is no super admin to seed');

            return Command::SUCCESS;
        }
        else if($existing->save()){
            $this->info('Create super admin successfully');
            return Command::SUCCESS;
        }

        $this->info('Failed to create super admin');
        return Command::FAILURE;
        
    }
}
