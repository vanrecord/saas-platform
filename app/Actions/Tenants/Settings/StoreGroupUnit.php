<?php
namespace App\Actions\Tenants\Settings;

use App\Models\Unit;
use App\Repositories\GroupUnits\GroupUnitRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use Throwable;

class StoreGroupUnit 
{
    protected $group_unit;

    public function __construct(){  
        $this->group_unit = App(GroupUnitRepository::class);
    }
    public function save( array $input){
        $request = Request::validate([
            'name_native' => $input['name_native'] ? 'max:100|unique:group_units,name_native,'.($input['id']??'null').',id,deleted_at,NULL' : 'nullable',
            'name_international' => 'required|max:255|unique:group_units,name_international,'.($input['id']??'null').',id,deleted_at,NULL',
            'order'  => 'nullable|integer|min:0|max:999|unique:group_units,order,'.($input['id']??'null').',id,deleted_at,NULL',
        ],[
            'name_international.required' => 'The name en field is required.',
            'name_international.max' => 'The name en may not be greater than 255 characters.',
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
                $this->group_unit->update($input['id'],$input);
            }else{
                $input['created_by'] = $user_id;
                $input['updated_by'] = $user_id;
                $unit = $this->group_unit->create($input);
            }

        } catch (\Throwable $th) {
            return redirect()->back()->withErrors([
                'create' => 'Unit, there was an error'
             ]);
        }
        
    }
}