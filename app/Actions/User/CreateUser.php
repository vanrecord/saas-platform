<?php

namespace App\Actions\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use App\Actions\Fortify\PasswordValidationRules;
use App\Repositories\UserRepository;

class CreateUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function __construct()
    {
        $this->user = App(UserRepository::class);
    }

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

        $currentUserId = auth()->user()?->id ?? auth('tenant')->user()?->id;

        $user = $this->user->create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'level_access' => $input['level_access'] ?? 0,
            'email_verified_at' => !empty($input['email_verified_at']) ? now() : now(),
            'created_by' => $currentUserId,
            'updated_by' => $currentUserId
        ]);

        if (!empty($input['role'])) {
            $user->syncRoles($input['role']);
        }
    }
}
