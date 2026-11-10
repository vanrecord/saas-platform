<?php

namespace App\Actions\Central;

use App\Models\Tenant;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Repositories\DistrictRepository;
use App\Repositories\ProvinceRepository;

class UpdateDomain
{
    protected $province;
    protected $district;

    public function __construct()
    {
        $this->province = App(ProvinceRepository::class);
        $this->district = App(DistrictRepository::class);
    }
    public function update($id, $domain, array $input)
    {
        $user_id = auth()->user()->id;
        $user = auth()->user();

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

        $request = Validator::make($input, [
            'company' => ['required', 'max:100'],
            'domain' => ['required', 'regex:/^[a-zA-Z0-9\-]+$/', 'max:50', Rule::unique('domains')->ignore($id)],
            'site_number' => [
                'required', 'max:100', Rule::unique('domains')
                    // ->where('user_id', $user_id)
                    ->whereIn('user_id', $user->getRoot()->getDescendantsAndSelf()->pluck('id','name')->toArray())
                    ->ignore($id)
            ],
            'package_id' => ['nullable'],
            'user_limit'=> ['integer', 'nullable', 'min:0'],
            'employee_limit'=> ['integer', 'nullable', 'min:0'],
            'number_group_telegram_broadcast' => ['nullable', 'numeric','min:0'],
            'event_limit' => ['integer', 'nullable', 'min:1', Rule::requiredIf(fn() => !empty($input['event_limit_period_type']))],
            'event_limit_period_type' => ['string', 'nullable', Rule::requiredIf(fn() => !empty($input['event_limit']))],
        ])->validate();

        try {
            $input['name_international'] = $input['company'];
            $input['province_id'] = !empty($input['province_id'])?$input['province_id']:null;
            $input['district_id'] = !empty($input['district_id'])?$input['district_id']:null;
            $input['number_group_telegram_broadcast'] = $input['number_group_telegram_broadcast'];
            // dd($input['number_group_telegram_broadcast']);
            $input['event_limit'] = !empty($input['event_limit']) ? $input['event_limit'] : null;
            $input['event_limit_period_type'] = !empty($input['event_limit_period_type']) ? $input['event_limit_period_type'] : null;
            $tenant = Tenant::find($input['tenant_id']);
            $tenant->update($input);
            // $tenant->run(function($q) use ($input){
                // $data_user = [
                //     'name' => $q->name,
                //     'email' => $q->email,
                //     'password' => bcrypt($q->password),
                //     'level_access' => User::SUPER_ADMIN
                // ];
                // $tenant_admin = app(User::class)->where('email',$data_user['email'])->first();
                // if($tenant_admin){
                //     $tenant_admin->update($data_user);
                // }else{
                //     app(User::class)->create($data_user);
                // }
            // });
            // End update company tenancy
            $validateDomain = $domain->query()
                ->where('domain', $input['domain'] . '.' . config('tenancy.tenancy_domains')[0])
                ->where('id', '!=', $id)
                ->first();
            if ($validateDomain) {
                return ['success' => false, 'error' => 'Domain already exists.'];
            }
            $domain = $domain->update($id,[
                'company' => $input['company'],
                'domain' => $input['domain'],
                'is_active' => $input['is_active'],
                // 'user_id' => $user_id,
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
                'updated_by'  => $user_id
            ]);
            
            // Sync hospital name to tenant
            try {
                $tenant->run(function ($tenant) {
                    $hospital = \App\Models\Hospital::first();
                    if ($hospital) {
                        $hospital->update([
                            'name' => $tenant->company,
                        ]);
                    }
                });
            } catch (\Throwable $e) {
                info('Tenant run sync hospital skipped: ' . $e->getMessage());
            }
            // End sync hospital to tenant

            return $domain;
        } catch (\Throwable $th) {
            info('Update domain error: ' . $th->getMessage() . ' ' . $th->getFile() . ':' . $th->getLine());
            return ['success' => false, 'error' => $th->getMessage()];
        }
    }
}
