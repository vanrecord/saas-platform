<?php
namespace App\Actions\Tenants\ThirdParty;

use Illuminate\Support\Facades\Request;
use App\Repositories\Suppliers\SupplierRepository;

class StoreSupplier
{
    protected $supplier;

    public function __construct(){
        $this->supplier = App(SupplierRepository::class);
    }
    public function store($request){
        Request::validate([
            'name'              => 'required|max:100|unique:suppliers,name,'.($request->id??'null').',id,deleted_at,NULL',
            'previous_credit'   => 'required|numeric|min:0|integer|regex:/^[0-9]{1,20}$/i',
            'phone'             => $request->phone          ? 'regex:/^[0-9]{3,15}$/i' : '',
            'credit_term'       => $request->credit_term    ? 'integer|min:0|max:99999999' : 'nullable',
            'vat_no'            => $request->vat_no         ? 'integer|min:0|max:99999999' : 'nullable',
            'email'             => $request->email          ? 'regex:/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i' : '',
        ],[
            'phone.regex' => 'Phone must be number between 3 and 15 characters.',
            'previous_credit.regex' => 'Previous credit must be number between 5 and 20 characters.',
            'credit_term.regex' => 'Credit term allowed only number.',
            'vat_no.regex' => 'VAT No. allowed only number.',
            'vat_no.min' => 'VAT No. must be at least 1 characters.',
            'vat_no.max' => 'VAT No. must be at most 9 characters.',
            'vat_no.integer' => 'VAT No. must be integer.',
            'email.regex' => 'Email must be valid email address.',
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
