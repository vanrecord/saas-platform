<?php

namespace App\Http\Requests\Tenant;

use App\Models\Commune;
use App\Models\District;
use App\Models\Village;
use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'province_id' => ['nullable', 'integer'],
            'district_id' => ['nullable', 'integer'],
            'commune_id' => ['nullable', 'integer'],
            'village_id' => ['nullable', 'integer'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $provinceId = $this->input('province_id');
            $districtId = $this->input('district_id');
            $communeId = $this->input('commune_id');
            $villageId = $this->input('village_id');

            // Verify District belongs to selected Province
            if ($districtId && !$provinceId) {
                $validator->errors()->add('district_id', 'Province must be selected before choosing a district.');
            } elseif ($districtId && $provinceId) {
                $validDistrict = District::where('id', $districtId)->where('province_id', $provinceId)->exists();
                if (!$validDistrict) {
                    // Also check central fallback if tenant table has no records
                    $validDistrict = tenancy()->central(fn () => District::where('id', $districtId)->where('province_id', $provinceId)->exists());
                    if (!$validDistrict) {
                        $validator->errors()->add('district_id', 'The selected district does not belong to the selected province.');
                    }
                }
            }

            // Verify Commune belongs to selected District
            if ($communeId && !$districtId) {
                $validator->errors()->add('commune_id', 'District must be selected before choosing a commune.');
            } elseif ($communeId && $districtId) {
                $validCommune = Commune::where('id', $communeId)->where('district_id', $districtId)->exists();
                if (!$validCommune) {
                    $validCommune = tenancy()->central(fn () => Commune::where('id', $communeId)->where('district_id', $districtId)->exists());
                    if (!$validCommune) {
                        $validator->errors()->add('commune_id', 'The selected commune does not belong to the selected district.');
                    }
                }
            }

            // Verify Village belongs to selected Commune
            if ($villageId && !$communeId) {
                $validator->errors()->add('village_id', 'Commune must be selected before choosing a village.');
            } elseif ($villageId && $communeId) {
                $validVillage = Village::where('id', $villageId)->where('commune_id', $communeId)->exists();
                if (!$validVillage) {
                    $validVillage = tenancy()->central(fn () => Village::where('id', $villageId)->where('commune_id', $communeId)->exists());
                    if (!$validVillage) {
                        $validator->errors()->add('village_id', 'The selected village does not belong to the selected commune.');
                    }
                }
            }
        });
    }

    public function attributes(): array
    {
        return [
            'name' => 'Supplier Name',
            'phone' => 'Phone',
            'email' => 'Email',
            'contact_person' => 'Contact Person',
            'address' => 'Address',
            'province_id' => 'Province',
            'district_id' => 'District',
            'commune_id' => 'Commune',
            'village_id' => 'Village',
        ];
    }
}
