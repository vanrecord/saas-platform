<?php
namespace App\Actions\Tenants\Settings;

use Illuminate\Support\Facades\Request;
use App\Repositories\PaymentMethods\PaymentMethodRepository;

class StorePaymentMethod {
    protected $paymantMethod;

    public function __construct(){  
        $this->paymantMethod = App(PaymentMethodRepository::class);
    }

    public function store(Array $input){
        $Request = Request::validate([
            'name' => ['required','max:100'],
            'code'  => ['required', 'max:50'],
            'order'  => ['max:999'],
        ]);

        $user_id = auth()->user()->id;
        $input['created_by'] = $user_id;
        $input['updated_by'] = $user_id;
        if(isset($input['id'])){
            unset($input['created_by']);
            $this->paymantMethod->update($input['id'],$input);
            $msg = 'Lab updated successfully';
        }else{
            $this->paymantMethod->create($input);
            $msg = 'Lab created successfully';
        }
        return $msg;
    }
}