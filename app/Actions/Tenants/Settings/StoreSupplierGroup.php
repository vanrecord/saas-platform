<?php
namespace App\Actions\Tenants\Settings;

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
    public function store( array $input){
        $request = Request::validate([
            'name' => ['required', 'max:100'],
        ]);
        try {
            $user_id = auth()->user()->id;
            
            if(isset($input['id']) && !empty($input['id'])){
                $input['updated_by'] = $user_id;
                $input = $this->supplier_group->update($input['id'],$input);
            }else{
                $input['created_by'] = $user_id;
                $input['updated_by'] = $user_id;
                $input = $this->supplier_group->create($input);
            }
            return $input;
        } catch (\Throwable $th) {
            dd($th);
            return redirect()->back()->withErrors([
                'create' => 'Supplier group, there was an error'
            ]);
        }
        
    }
}