<?php
namespace App\Actions\Tenants\Settings;

use App\Repositories\Units\UnitRepository;
use Illuminate\Support\Facades\Request;

class CreateNewUnit 
{
    protected $unit;

    public function __construct(){  
        $this->unit = App(UnitRepository::class);
    }
    public function create( array $input){
        $id = Request::get('id');
        Request::validate([
            'name_native' => 'max:255'.(
                $input['name_native']
                    ? '|unique:units,name_native,'. $id ??= 'NULL'
                    : ''
            ),
            'name_international' => 'required|max:255'.(
                $input['name_international']
                    ? '|unique:units,name_international,'. $id ??= 'NULL'
                    : ''
            ),
            'code'  => 'required|max:50'.(
                $input['code']
                    ? '|unique:units,code,'. $id ??= 'NULL'
                    : ''
            ),
            'order'  => 'max:999'.(
                $input['order']
                    ? '|unique:units,order,'. $id ??= 'NULL'
                    : ''
            ),
            'group_unit' => 'required',
            'description' => 'max:255',
        ]);
        try {
            $user_id = auth()->user()->id;
            if(isset($input['is_active']) && $input['is_active']){
                $input['is_active'] = 'true';
            }else{
                $input['is_active'] = 'false';
            }

            if(isset($input['id']) && !empty($input['id'])){
                $input['updated_by'] = $user_id;
                $unit = $this->unit->update($input['id'],$input);
                $unit->groupUnits()->sync($input['group_unit']);
            }else{
                $input['created_by'] = $user_id;
                $input['updated_by'] = $user_id;
                $unit = $this->unit->create($input);
                $unit->groupUnits()->sync($input['group_unit']);
            }

        } catch (\Throwable $th) {
            dd($th);
            return redirect()->back()->withErrors([
                'create' => 'Unit, there was an error'
             ]);
        }
        
    }
}