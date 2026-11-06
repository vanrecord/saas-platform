<?php
namespace App\Actions\Tenants\ThirdParty;

use Illuminate\Support\Facades\Request;
use App\Repositories\CustomerGroups\CustomerGroupRepository;
use App\Repositories\CustomerGroupItems\CustomerGroupItemRepository;

class StoreCustomerGroup 
{
    protected $customer_group;

    public function __construct(){  
        $this->customer_group = App(CustomerGroupRepository::class);
        $this->customer_group_item = App(CustomerGroupItemRepository::class);
    }

    public function store($request){
        Request::validate([
            'name' => ['required', 'max:100' , "unique:customer_groups,name,".($request->id??'null').",id,deleted_at,NULL"],
        ]);
        $datas = $request->only(['name','description','is_active','is_default']);
        return $request->id
            ? $this->customer_group->update($request->id,$datas)
            : $this->customer_group->create($datas);
    }

    public function cancel($request)
    {
        if($request->delete == true){
            $this->customer_group->delete($request['id']);
        }else{
            $this->customer_group->update($request['customer_group']['id'],$request['customer_group']);
            \DB::table('customer_group_items')
                ->where('customer_group_id',$request['customer_group']['id'])
                ->delete();
            collect($request['items'])->each(function($item){
                unset($item['id']);
                $this->customer_group_item->create($item);
            });
        }
        return [
            'success' => true,
            'message' => 'Cancelled',
        ];
    }
}