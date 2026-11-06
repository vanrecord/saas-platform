<?php
namespace App\Actions\Tenants\Settings;

use App\Repositories\Lab\LabRepository;
use Illuminate\Support\Facades\Request;
use App\Repositories\Suppliers\SupplierRepository;

class StoreSupplier
{

    public function __construct(){
        $this->supplier = App(SupplierRepository::class);
    }
    public function store($request){
        Request::validate([
            'name' => ['required', 'max:100'],
            'previous_credit' => ['required', 'max:100'],
        ]);
        try {
            $user_id = auth()->user()->id;

            if(isset($request->id) && !empty($request->id)){
                unset($request->created_by);
                $supplier= $this->supplier->update($request->id,$request->except('logo','_token','id'));
                $supplier->labs()->sync($request->lab_id);
                $msg = 'Supplier updated successfully';
            }else{
                $request['created_by'] = $user_id;
                $request['updated_by'] = $user_id;
                $supplier = $this->supplier->create($request->except('logo','_token'));
                $supplier->labs()->sync($request->lab_id);
                $msg = 'Supplier created successfully';
            }
        } catch (\Throwable $th) {
            dd($th);
            return redirect()->back()->withErrors([
                'create' => 'Supplier, there was an error'
            ]);
        }

        return $msg;

    }
}
