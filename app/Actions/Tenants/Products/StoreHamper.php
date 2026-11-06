<?php
namespace App\Actions\Tenants\Products;

use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Hampers\HamperRepository;
use App\Repositories\Hampers\HamperDetailRepository;

class StoreHamper 
{
    protected $hamper;
    protected $hamperDetail;

    public function __construct()
    {
        $this->hamper = App(HamperRepository::class);
        $this->hamperDetail = App(HamperDetailRepository::class);
    }

    public function store($request)
    {
        $id = $request['id']??null;
        $rqId = $id?(",$id,id,deleted_at,NULL"):(",null,1,deleted_at,NULL");
        $validator = Validator::make($request, [
            'name_international' => 'required|unique:hampers,name_international'.$rqId,
            'code' => 'required|max:255|unique:hampers,code'.$rqId.'|unique:products,code|unique:product_unit,code',
            'sale_price' => 'numeric|min:0',
            'group_unit_id' => 'required|integer',
            'unit_id' => 'required|integer'
        ],[
            'unit_id.integer' => 'The unit id field is required.',
            'group_unit_id.integer' => 'The group unit id field is required.'
        ]);
        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()
            ], 200);
        }
        $datas = [
            'category_id' => $request['category_id'] != 'null'?$request['category_id'] :null,
            'code' =>  $request['code'] != 'null'?$request['code']:null,
            'name_international' => $request['name_international'] != 'null'?$request['name_international']:null,
            'name_native' => $request['name_native'] != 'null'?$request['name_native']:null,
            'is_active' => $request['is_active'] != 'null'?$request['is_active']:null,
            'for_sale' => $request['for_sale'] != 'null'?$request['for_sale']:null,
            'group_unit_id' => $request['group_unit_id'] != 'null'?$request['group_unit_id']:null,
            'unit_id' => $request['unit_id'] != 'null'?$request['unit_id']:null,
            'item_qty' => $request['item_qty'] != 'null'?$request['item_qty']:null,
            'cost_price' => $request['cost_price'] != 'null'?$request['cost_price'] :null,
            'estimate_price' => $request['estimate_price'] != 'null'?$request['estimate_price']:null,
            'sale_price' => $request['sale_price'] != 'null'?$request['sale_price']:null,
            'description' => $request['description'] != 'null'?$request['description']:null,
            'is_track_inventory' => $request['is_track_inventory'] != 'null'?$request['is_track_inventory'] :null,
            'created_by' => auth()->user()->id,
            'updated_by' => auth()->user()->id,
        ];
        if(isset($request['id'])){
            $hamperDetail = $this->hamperDetail->query()->where('hamper_id', '=', $request['id'])->count();
            if($hamperDetail <= 1 ){
                return response()->json([
                            'warning' => 'Please input at least 2 products !'
                        ]);
            }
            $hamper = $this->hamper->update($request['id'],$datas);
        }else{
            $hamper = $this->hamper->create($datas);
        }
        return response()->json($hamper);
    }

    public function storeItem($request)
    {   
        $id = !empty($request->id)?$request->id:'';
        $validateUnit = $id?(",$id,id,productable_id,$request->productable_id,hamper_id,$request->hamper_id"):(",null,1,productable_id,$request->productable_id,hamper_id,$request->hamper_id");
  
        $validator = Validator::make($request->all(), [
            'productable_id' => 'required',
            'unit_id' => 'required|unique:hamper_details,unit_id'.$validateUnit,
            'quantity' => 'numeric|min:1',
            'uom_number' => 'numeric|min:1'
        ],[
            'unit_id.unique' => 'Product unit already added',
            'productable_id.required' => 'The product is field required'
        ]);
        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()
            ], 200);
        }
        if($request->id){

            $hamperDetail = $this->hamperDetail->update($request->id,[
                'hamper_id' => $request->hamper_id,
                'productable_id' => $request->productable_id,
                'productable_type' => Product::class,
                'quantity' => $request->quantity,
                'cost_price' => $request->cost_price,
                'sale_price' => $request->sale_price,
                'updated_by' => auth()->user()->id,
                'unit_id' => $request->unit_id,
                'uom_number' => $request->uom_number,
            ]);
        }else{
            $hamperDetail = $this->hamperDetail->create([
                'hamper_id' => $request->hamper_id,
                'productable_id' => $request->productable_id,
                'productable_type' => Product::class,
                'quantity' => $request->quantity,
                'cost_price' => $request->cost_price,
                'sale_price' => $request->sale_price,
                'unit_id' => $request->unit_id,
                'uom_number' => $request->uom_number,
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,

            ]);
        }
        return response()->json($hamperDetail->load('productable','unit','productable.productUnit','productable.productUnit.unit'));
    }
    
    public function cancel($request)
    {
        if($request->delete == true){
            $this->hamper->delete($request['id']);
        }else{
            $this->hamper->update($request['hamper']['id'],$request['hamper']);
            \DB::table('hamper_details')
                ->where('hamper_id',$request['hamper']['id'])
                ->delete();
            collect($request['items'])->each(function($item){
                unset($item['id']);
                $this->hamperDetail->create($item);
            });
        }
        return [
            'success' => true,
            'message' => 'Cancelled',
        ];
    }
}