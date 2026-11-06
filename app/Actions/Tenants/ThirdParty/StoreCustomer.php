<?php
namespace App\Actions\Tenants\ThirdParty;

use Illuminate\Support\Facades\Request;
use App\Repositories\Customers\CustomerRepository;

class StoreCustomer
{
    protected $customer;

    public function __construct(){  
        $this->customer = App(CustomerRepository::class);
    }
    public function store( array $input){
        $id = $input['id']??null;
        $rqId = $id?(",$id,id,deleted_at,NULL"):(",null,1,deleted_at,NULL");
        Request::validate([
            'name' => ['required', 'max:100'],
            'customer_group_id' => ['required'],
            'age' => $input['age'] ? ['regex:/^[0-9]{1,3}$/i'] : '',
            'phone' => $input['phone'] ? ['regex:/^[0-9]{1,15}$/i','unique:customers,phone'.$rqId] : '',
        ]);
        if(isset($input['is_opening_balance']) && $input['is_opening_balance'] == true){
            Request::validate([
                'opening_balance_at' => ['required'],
                'amount' => ['required','numeric','min:0'],
            ]);
        }
        $user_id = auth()->user()->id;
        if(isset($input['id']) && !empty($input['id'])){
            $input['updated_by'] = $user_id;
            $customer = $this->customer->find($input['id']);
            if($customer->balanceables->isEmpty() && $input['is_opening_balance'] == true && isset($input['opening_balance_at']) && $input['opening_balance_at'] && $input['amount']){
                $customer->balanceables()->create([
                    'able_id' => $customer->id,
                    'able_type' => Customer::class,
                    'opening_balance_at' => $input['opening_balance_at'],
                    'currency' => $input['currency_id'],
                    'amount' => $input['amount']
                ]);
            }
            $customer = $this->customer->update($input['id'],$input);
        }else{
            $input['created_by'] = $user_id;
            $input['updated_by'] = $user_id;
            $customer = $this->customer->create($input);
            if(isset($input['opening_balance_at']) && $input['opening_balance_at'] && $input['amount']){
                $customer->balanceables()->create([
                    'able_id' => $customer->id,
                    'able_type' => Customer::class,
                    'opening_balance_at' => $input['opening_balance_at'],
                    'currency' => $input['currency_id'],
                    'amount' => $input['amount']
                ]);
            }

        }
        return $customer;
    }
}