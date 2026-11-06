<?php

namespace App\Actions\User;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Support\Facades\Request;

class UpdateUser  extends UpdateUserProfileInformation
{
    protected $user;

    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    public function update($user, array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ])->validate();

        if (
            $input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail
        ) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $currentUserId = auth()->user()?->id ?? auth('tenant')->user()?->id;
            $data = [
                'name' => $input['name'],
                'email' => $input['email'],
                'is_active' => $input['is_active'] ?? $user->is_active,
                'level_access' => $input['level_access'] ?? $user->level_access,
                'updated_by' => $currentUserId,
            ];

            if (array_key_exists('role', $input)) {
                $data['role'] = $input['role'];
            }

            if (array_key_exists('email_verified_at', $input)) {
                $data['email_verified_at'] = $input['email_verified_at'] ? now() : null;
            }

            $user->update($data);
        }

        if (!empty($input['role'])) {
            $user->syncRoles($input['role']);
        }
    }

    protected function updateVerifiedUser($user, array $input)
    {

        $user->update($user, [
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ]);

        $user->sendEmailVerificationNotification();
    }
}
