<?php

namespace App\Actions\Sector;

use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Jetstream;
use App\Repositories\SectorRepository;
use Illuminate\Support\Facades\Request;
use App\Models\Sector;
use Illuminate\Validation\Rule;

class UpdateSector 
{
    protected $sector;

    public function __construct(SectorRepository $sector)
    {
        $this->sector = $sector;
    }

    public function update($sector, array $input)
    {
        Request::validate([
            'code' => ['required','string', Rule::unique('sectors')->ignore($sector->id)->whereNull('deleted_at')],
            'name_native' => ['required', 'string', 'max:255'],
            'name_international' => ['required', 'string', 'max:255'],
        ]);

        $this->sector->update($sector, [
            'code' => $input['code'],
            'name_native' => $input['name_native'],
            'name_international' => $input['name_international'],
            'is_active' =>  $input['is_active']
        ]);
    }
}
