<?php
namespace App\Actions\Central;

use App\Actions\Fortify\PasswordValidationRules;
use App\Jobs\CreateRole;
use App\Models\Domain;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;
use Throwable;

class CreateNewDomain 
{
    use PasswordValidationRules;
    public function create( array $input){
        $user_id = auth()->user()->id;
        $user = auth()->user();
        $bool_domain = true;
        if(empty($user->parent_id)){
            $bool_domain = Domain::where('user_id',$user_id)->first()?true:false;
        }
        if(empty($bool_domain)){
            $input['password'] = $user->password;
            $input['password_confirmation'] = $user->password;
            $input['name'] = $user->name;
            $input['email'] = $user->email;
            $input['global_id'] = $user->global_id;
        }
        $input['email_verified_at'] = now();

        $input['event_limit'] = !empty($input['event_limit']) ? (int)$input['event_limit'] : null;
        $input['event_limit_period_type'] = (!empty($input['event_limit_period_type']) && is_string($input['event_limit_period_type']) && $input['event_limit_period_type'] !== '0') ? $input['event_limit_period_type'] : null;
        $input['parent_domain_id'] = !empty($input['parent_domain_id']) ? $input['parent_domain_id'] : null;
        $input['package_id'] = !empty($input['package_id']) ? $input['package_id'] : null;
        $input['sector_id'] = !empty($input['sector_id']) ? $input['sector_id'] : null;
        $input['province_id'] = !empty($input['province_id']) ? $input['province_id'] : null;
        $input['district_id'] = !empty($input['district_id']) ? $input['district_id'] : null;
        $input['user_limit'] = !empty($input['user_limit']) ? (int)$input['user_limit'] : null;
        $input['employee_limit'] = !empty($input['employee_limit']) ? (int)$input['employee_limit'] : null;
        $input['number_group_telegram_broadcast'] = isset($input['number_group_telegram_broadcast']) && $input['number_group_telegram_broadcast'] !== '' ? (int)$input['number_group_telegram_broadcast'] : 0;

        $request = Validator::make($input,[
            'company' => ['required', 'max:100'],
            'site_number' => ['required', 'max:100',Rule::unique('domains')->where(function ($query) use ($user_id, $user) {
                               return $query->whereIn('user_id', $user->getRoot()->getDescendantsAndSelf()->pluck('id','name')->toArray());
            })],
            'domain' => ['required','regex:/^[a-zA-Z0-9\-]+$/', 'max:50'],
            'name'  => ['required', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => $this->passwordRules(),
            'latlng' => ['nullable'],
            'range' => ['nullable'],
            'package_id' => ['nullable'],
            'sector_id' => ['nullable'],
            'parent_domain_id' => Rule::requiredIf(function () use ($user) {
                return !$user->isSuperAdmin()&&count($user->domains);
            }),
            'user_limit'=> ['integer', 'nullable', 'min:0'],
            'employee_limit'=> ['integer', 'nullable', 'min:0'],
            'number_group_telegram_broadcast' => ['nullable', 'numeric','min:0'],
            'event_limit' => ['integer', 'nullable', 'min:1', Rule::requiredIf(fn() => !empty($input['event_limit_period_type']))],
            'event_limit_period_type' => ['string', 'nullable', Rule::requiredIf(fn() => !empty($input['event_limit']))],
        ],[
            'parent_domain_id.required' => 'The parent field is required!'
        ])->validate();
        $domain = Domain::where('domain', $input['domain'].'.'.config('tenancy.tenancy_domains')[0])->first();
        if($domain) {
            return ['success' => false, 'error' => 'Domain already exists.'];
        }
        try {
            if(!empty($bool_domain)){
                $input['password'] = Hash::make($input['password']);
            }
            if(!empty($input['allow_create_domain'])){
                if(!empty($user) && empty($bool_domain)){
                    $user->update($input);
                }else{
                    Validator::make($input, [
                        'email' => ['unique:users'],
                    ],[
                        'email.unique' => 'The email has already been taken with allow create domain.',
                    ])->validate();
                    $input['parent_id'] = $user_id;
                    $input['global_id'] = (string) Uuid::uuid4();
                    $input['created_by']= $user_id;
                    $input['updated_by'] = $user_id;
                    $user = User::create($input);
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
                    $role = app(Role::class)->where('name', $attach['name'])->where('guard_name', 'web')->first();
                    if (!$role) {
                        CreateRole::dispatchSync($attach);
                        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
                    }
                    $user->syncRoles($attach['name']);
                }
            }
            $input['name_international'] = $input['company']; 
            $input['province_id'] = !empty($input['province_id'])?$input['province_id']:null;
            $input['district_id'] = !empty($input['district_id'])?$input['district_id']:null;
            $input['user_limit'] = !empty($input['user_limit'])?$input['user_limit']:null;
            $input['employee_limit'] = !empty($input['employee_limit'])?$input['employee_limit']:null;
            $input['number_group_telegram_broadcast'] = $input['number_group_telegram_broadcast'];
            $input['event_limit'] = !empty($input['event_limit']) ? $input['event_limit'] : null;
            $input['event_limit_period_type'] = !empty($input['event_limit_period_type']) ? $input['event_limit_period_type'] : null;
            $input['range'] = !empty($input['range']) ? $input['range'] : null;
            $input['latlng'] = !empty($input['latlng']) ? $input['latlng'] : null;
            $tenant = Tenant::create($input);
            $tenant->createDomain([
                'company' => $input['company'],
                'domain' => $input['domain'],
                'is_active' => $input['is_active'],
                'user_id' => $user->id,
                'site_number' => $input['site_number'],
                'parent_id' => !empty($input['parent_domain_id']) ? $input['parent_domain_id'] : null,
                'package_id' => !empty($input['package_id']) ? $input['package_id'] : null,
                'sector_id' => !empty($input['sector_id']) ? $input['sector_id'] : null,
                'phone' => !empty($input['phone']) ? $input['phone'] : null,
                'allow_create_domain' => !empty($input['allow_create_domain']),
                'range' => !empty($input['range']) ? $input['range'] : null,
                'latlng' => !empty($input['latlng']) ? json_encode($input['latlng']) : null,
                'province_id' => !empty($input['province_id']) ? $input['province_id'] : null,
                'district_id' => !empty($input['district_id']) ? $input['district_id'] : null,
                'created_by' => $user_id,
                'updated_by' => $user_id
            ]);

            // Ensure Hospital record exists in the tenant database
            try {
                $tenant->run(function ($t) use ($input) {
                    $firstUser = \App\Models\UserTenant::orderBy('id', 'asc')->first();
                    if ($firstUser) {
                        $hospitalName = $input['company'] ?? $t->company ?? 'Hospital';
                        \App\Models\Hospital::firstOrCreate(
                            ['user_id' => $firstUser->id],
                            ['name' => $hospitalName]
                        );
                    }
                });
            } catch (\Throwable $e) {
                info('CreateNewDomain hospital verification: ' . $e->getMessage());
            }

            return $tenant;
        } catch (\Throwable $th) {
            info('Create domain error: ' . $th->getMessage() . ' ' . $th->getFile() . ':' . $th->getLine());
            return ['success' => false, 'error' => $th->getMessage()];
        }
    }
}