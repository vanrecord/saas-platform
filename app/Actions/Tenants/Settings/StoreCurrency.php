<?php
namespace App\Actions\Tenants\Settings;

use Illuminate\Support\Facades\Request;
use App\Repositories\Currencies\CurrencyRepository;
use \App\Models\Currency;

class StoreCurrency {
    protected $currency;

    public function __construct(){  
        $this->currency = App(CurrencyRepository::class);
    }

    public function store(Array $input){
        $ordervalide = $input['order']?'|unique:currencies,order':'';
        $request = Request::validate([
            'name'          => ['required','max:100'],
            'code'          => ['required', 'max:50'],
            'exchange_rate' => ['nullable','numeric'],
            'order'         => 'max:999'.$ordervalide.(isset($input['id'])?','. $input['id']:',NULL').',id,deleted_at,NULL',
        ]);

        try {
            $user_id = auth()->user()->id;
            $input['created_by'] = $user_id;
            $input['updated_by'] = $user_id;
            
            if($input['is_default']){
                $input['round'] = null;
            }
            
            if(isset($input['id'])){
                // unset($input['created_by']);
                if($input['is_active']){
                    $countActive = $this->currency->query()->where('is_active', true)->where('id', '!=', $input['id'])->count();
                    if($countActive >= 3){
                        return ['success'=> false, 'msg' => 'Only 3 currency can active!'];
                    }
                }
                currency()->update($input['id'],$input);
                $this->currency->forgetCache();
                $msg = 'Currency updated successfully';
            }else{
                $currency = \Currency::create($input);
                $msg = 'Currency created successfully';
            }
            return $msg;

        } catch (\Throwable $th) {
            return redirect()->back()->withErrors([
                'create' => 'Currency, there was an error'
             ]);
        }
    }

}