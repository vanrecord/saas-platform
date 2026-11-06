<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Repositories\UserRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Models\UserTenant;
use App\Models\User;

class CreateTenantAdmin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Tenant $tenant)
    {
        $this->user = app(UserRepository::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->tenant->run(function ($tenant) use (&$user) {
            $name = $tenant->name ?? 'Admin';
            $email = $tenant->email ?? ('admin@' . $tenant->id . '.local');
            $password = $tenant->password;

            if (empty($password)) {
                $centralUser = User::where('email', $email)->first() ?? auth()->user();
                $password = $centralUser?->password ?? \Illuminate\Support\Facades\Hash::make('password123');
            }

            $userData = [
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'level_access' => 1,
            ];

            if (!empty($tenant->global_id)) {
                $userData['global_id'] = $tenant->global_id;
            }

            UserTenant::withoutEvents(function() use ($userData, &$user) {
                $existing = UserTenant::where('email', $userData['email'])->first();
                if ($existing) {
                    $existing->update($userData);
                    $user = $existing;
                } else {
                    $user = UserTenant::create($userData);
                }
            });

            // create user dev for tenant
            try {
                \Artisan::call('py:seed-user-dev-tenant', [
                    '--tenants' => [$tenant->id] 
                ]);
            } catch (\Throwable $e) {
                // Ignore if command not needed
            }
        });
    }
}
