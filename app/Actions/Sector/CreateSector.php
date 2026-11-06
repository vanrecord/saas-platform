<?php

namespace App\Actions\Sector;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Jetstream;
use App\Repositories\SectorRepository;
use Illuminate\Validation\Rule;

class CreateSector
{

    public function __construct()
    {
        $this->sector = App(SectorRepository::class);
    }

    public function create(array $input)
    {
        Validator::make($input, [
            'code' => ['required','string',Rule::unique('sectors')->where(fn ($query) => $query->where('code', $input['code'])->whereNull('deleted_at'))],
            'name_native' => ['required', 'string', 'max:255'],
            'name_international' => ['required', 'string', 'max:255'],
        ])->validate();
        $sector = $this->sector->create([
            'code' => $input['code'],
            'name_native' => $input['name_native'],
            'name_international' => $input['name_international'],
            'is_active' =>  $input['is_active']
        ]);

    }
}
