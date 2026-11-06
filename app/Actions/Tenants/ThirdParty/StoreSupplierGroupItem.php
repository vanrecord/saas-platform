<?php
namespace App\Actions\Tenants\ThirdParty;

use Throwable;
use App\Repositories\Products\ProductRepository;
use App\Repositories\SupplierGroups\SupplierGroupRepository;

class StoreSupplierGroupItem 
{
    protected $supplier_group;

    public function __construct(){
        $this->product = App(ProductRepository::class);
        $this->supplier_group = App(SupplierGroupRepository::class);
    }
    public function store($request){
        
        try {
            $user_id = auth()->user()->id;
            $supplier_group = $this->supplier_group->find($request->id);
            $products = $request->product_id;
            // $product_price = \DB::table('purchase_order_details')->whereIn('purchase_order_details.product_id', $products)
            //                             ->pluck('purchase_order_details.unit_price', 'purchase_order_details.product_id')->toArray();
            foreach($products as $key => $value){
                // $price = !empty($product_price[$value])?$product_price[$value]:0;
                $data[$value] = [       
                                            'purchase_price' => 0,
                                            'created_by' => $user_id,
                                            'updated_by' => $user_id,
                                        ];
            }
            $supplier_group->products()->sync($data, false);
            return $supplier_group->products()->orderBy('id','asc')->get();
        } catch (\Throwable $th) {
           dd($th);
            return redirect()->back()->withErrors([
                'create' => 'Supplier group, there was an error'
            ]);
        }
        
    }
}