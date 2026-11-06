<?php

namespace App\Actions\Tenants\Company;

use App\Models\Company;
use Illuminate\Support\Facades\Request;
use App\Repositories\Addresses\AddressRepository;
use App\Repositories\Companies\CompanyRepository;
use Illuminate\Support\Facades\Validator;

class SaveAddress
{
    protected $company;
    protected $address;

    public function __construct()
    {
        $this->company = App(CompanyRepository::class);
        $this->address   = App(AddressRepository::class);
    }
    public function create(array $input)
    {
        $user_id   = auth()->user()->id;
        $company = $this->company->first();
        $address  = $this->address;
        $datas = [
            'addressable_id'       => $company->id ?? null,
            'addressable_type'   => Company::class,
            'country_id'               => $input['country_id'] ?? null,
            'street_address'        => $input['street_address'] ?? null,
            'street_address_two' => $input['street_address_two'] ?? null,
            'state'                       => $input['state'] ?? null,
            'postal_code'            => $input['postal_code'] ?? null,
            'city'                         => $input['city'] ?? null,
            'created_by'             => $user_id,
            'updated_by'            => $user_id,
        ];
        if ($input['id']) {
            unset($datas['created_by']);
            $address->update($input['id'], $datas);
            $msg = 'Address updated successfully';
        } else {
            $address->create($datas);
            $msg = 'Address created successfully';
        }
        return ['success'=> true];
    }
}
