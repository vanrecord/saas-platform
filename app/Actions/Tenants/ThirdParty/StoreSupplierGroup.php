<?php
namespace App\Actions\Tenants\ThirdParty;

use Throwable;
use App\Models\Unit;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use App\Repositories\SupplierGroups\SupplierGroupRepository;

class StoreSupplierGroup 
{
    protected $supplier_group;

    public function __construct(){  
        $this->supplier_group = App(SupplierGroupRepository::class);
    }
    public function store($request){
        Request::validate([
            'name' => 'required|max:100|unique:supplier_groups,name,'.($request->id??'null').',id,deleted_at,NULL',
        ]);
        try {
            $user_id = auth()->user()->id;
            $data = [
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => true,
                'created_by' => $user_id,
                'updated_by' => $user_id
            ];
            
            if(isset($request->id) && !empty($request->id)){
                unset($data['created_by']);
                $supplier_group = $this->supplier_group->update($request->id,$data);
            }else{
                $supplier_group = $this->supplier_group->create($data);
            }
            return $supplier_group;
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors([
                'create' => 'Supplier group, there was an error'
            ]);
        }
        
    }
}