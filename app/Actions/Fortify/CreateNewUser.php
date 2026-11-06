<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use App\Actions\Fortify\PasswordValidationRules;
use App\Jobs\CreateRole;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
        
        $this->attachRoleVerified($user->id);

        return $user;
    }

    public function attachRoleVerified($id)
    {
        $attachPerms = $this->attachRole();
        $role = app(Role::class)->where('name', $attachPerms['name'])->where('guard_name', 'web')->first();
        $user = app(User::class)->find($id);
        if ($role == null) {
            CreateRole::dispatchSync($attachPerms);
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        }
        $role_old = $user->roles->pluck('name')->toArray();
        $role_old[] = $attachPerms['name'];
        $user->syncRoles(array_unique($role_old));
    }

    public function attachRole()
    {
        $attach = [
            'name' =>'owner',
            'perms' =>[
                0 => "dashboard.index",
                1 => "domain.index",
                2 => "domain.create",
                3 => "domain.edit",
                4 => "domain.store",
                5 => "domain.update",
                6 => "domain.destroy",
            ],
            'id'=>null
        ];
        return $attach;
    }
}
