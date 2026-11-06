<?php
namespace App\Actions\Tenants\ThirdParty;

use Illuminate\Support\Facades\Request;
use App\Repositories\Warehouses\WarehouseRepository;
use App\Models\Warehouse;

class StoreWarehouse {
    protected $warehouse;

    public function __construct(){  
        $this->warehouse = App(WarehouseRepository::class);
    }

    public function store($input){
        Request::validate([
            'name' =>['required','max:100', function ($attribute, $value, $fail) use ($input) {
                $get = $this->warehouse->query()
                    ->when(isset($input['id']), function ($query) use ($input) {
                        $query->where('id', '!=', $input['id']);
                    })
                    ->where('name', $input['name'])
                    ->when($input['type'], function ($query) use ($input) {
                        if($input['type'] == Warehouse::TYPE_LOCATION){
                            $query->where('type', $input['type'])
                                ->where('parent_id', $input['parent_id']);
                        }
                    })
                    ->first();
                if($get){
                    return $fail('The name has already been taken.');
                }
            }],
        ],[
            'name.required' => 'The name en field is required.',
            'name.max' => 'The name en may not be greater than 100 characters.'
        ]);
        try {
            $user_id = auth()->user()->id;
            $input['created_by'] = $user_id;
            $input['updated_by'] = $user_id;
            if($input['parent_id'] == null) {
                $input['type'] = Warehouse::TYPE_STOCK;
            }else if($input['type'] == null) {
                $input['type'] = Warehouse::TYPE_SUB_STOCK;
            }
            if(isset($input['id'])){
                unset($input['created_by']);
                $this->warehouse->update($input['id'],$input);
            }else{
                $this->warehouse->create($input);
            }
        }catch(\Exception $e){
            return redirect()->back()->withErrors([
                'create' => 'Warehouse, there was an error'
            ]);
        }
    }
}