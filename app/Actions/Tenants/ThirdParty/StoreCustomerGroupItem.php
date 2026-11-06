<?php
namespace App\Actions\Tenants\ThirdParty;

use App\Models\Product;
use App\Repositories\Products\ProductRepository;
use App\Repositories\Currencies\CurrencyRepository;
use Illuminate\Support\Facades\Validator;
use App\Repositories\CustomerGroups\CustomerGroupRepository;
use App\Repositories\ProductUnit\ProductUnitRepository;
use App\Repositories\CustomerGroupItems\CustomerGroupItemRepository;

class StoreCustomerGroupItem 
{
    protected $product;
    protected $currency;
    protected $supplier_group;

    public function __construct(){
        $this->product   = App(ProductRepository::class);
        $this->currency = App(CurrencyRepository::class);
        $this->customer_group = App(CustomerGroupRepository::class);
        $this->product_unit = App(ProductUnitRepository::class);
        $this->customer_group_item = App(CustomerGroupItemRepository::class);
    }
    public function store($request){
        $validator = Validator::make($request->all(), [
            'additional_price' => $request->additional_price ? 'numeric|min:0' : 'nullable',
            'product_id' => 'required',
        ],[
            'product_id.required' => 'Please select item',
        ]);
        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ], 200);
        }
        try {
            $customer_group = $this->customer_group->findBy('id',$request->id);
            $currency = $this->currency->findBy('is_default',true);
            // $product_unit = $this->product_unit->findWhereIn(['product_id',$request->product_id]);
            $items = \DB::table('customer_group_items')
                ->where([
                    ['customer_group_id',$customer_group->id],
                    ['priceable_id',$request->product_id],
                    ['unit_id',$request->unit_id],
                    ['priceable_type', Product::class]])
                ->get();
            if(count($items) > 0){
                return response()->json([
                    'duplicate' => 'Item has been added can not duplicate'
                ], 200);
            }
            // $product_unit->each(fn ($value) => 
                $this->customer_group_item->create([
                    'customer_group_id' => $customer_group->id,
                    'priceable_id'      => $request->product_id,
                    'priceable_type'    => Product::class,
                    'unit_id'           => $request->unit_id,
                    'additional_type'   => $request->additional_type??null,
                    'additional_sign'   => $request->additional_sign??null,
                    'additional_price'  => $request->additional_price??null,
                    'ref_price'         => $request->ref_price??null,
                    'currency'          => $currency->code,
                    'sale_price'        => $request->sale_price??null,
                ]);
            // );
            return response()->json($customer_group->customerGroupItem->load(['product.category','product.units','unit']));
        } catch (\Throwable $th) {
           dd($th);
            return redirect()->back()->withErrors([
                'create' => 'Customer group item, there was an error'
            ]);
        }
        
    }
}