<?php
namespace App\Actions\Tenants\Settings;

use App\Models\PriceHistory;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Products\ProductRepository;
use App\Repositories\Catalogs\CatalogDetailRepository;
use App\Repositories\ProductUnit\ProductUnitRepository;
use App\Models\Product;
use App\Repositories\PriceHistory\PriceHistoryRepository;

class StoreProduct
{
    protected $product;
    protected $productUnit;
    protected $catalogDetail;
    protected $priceHistory;

    public function __construct()
    {
        $this->product         = App(ProductRepository::class);
        $this->productUnit   = App(ProductUnitRepository::class);
        $this->catalogDetail = App(CatalogDetailRepository::class);
        $this->priceHistory   = App(PriceHistoryRepository::class);
    }

    public function store($input){
        $validateGenalData = $this->validateGenalData($input);
        if($validateGenalData){
            return $validateGenalData;
        }

        $input_unit = !empty($input['form_unit'])?json_decode($input['form_unit']):[];
        $validateFormUnit = $this->validateFormUnit($input_unit,$input['id']??null);
        if($validateFormUnit['success'] == false){
            return $validateFormUnit;
        }
        $data_units = $validateFormUnit['data_unit'];

        $product = $input;
        $datas = [
            'name_international' => $product['name_international'],
            'name_native'        => $product['name_native'] !="null"? $product['name_native']:null,
            'code'               => $product['code'] != "null" ? $product['code'] : null,
            'qty_reorder'        => $product['qty_reorder'] != "null" ? $product['qty_reorder'] : null,
            'is_active'          => $product['is_active'],
            'description'        => $product['description'] != "null" ? $product['description'] : null,
            'group_unit_id'      => $product['group_unit_id'] != "null" ? $product['group_unit_id']:null,
            'category_id'        => $product['category_id'] != "null"?$product['category_id']:null,
            'lab_id'             => $product['lab_id']!= "null"?$product['lab_id']:null,
            'composition'        => $product['composition'] != "null" ? $product['composition'] : null,
            'for_sale'           => $product['for_sale'],
            'expire_alert'       => $product['expire_alert'] != "null"?$product['expire_alert']:null,
            'is_track_expire'    => $product['is_track_expire'],
            'is_track_inventory' => $product['is_track_inventory'],
            'max_purchase'       => $product['max_purchase'] != "null"?$product['max_purchase']:null,
            'type'               => $product['type'] != "null"?$product['type']:null,
            'item_level_id'      => $product['item_level_id'] != "null"?$product['item_level_id']:null,
            'tax'                => $product['tax'] != null && $product['tax'] != "null" ?$product['tax']:0,
            'tax_method'         => $product['tax_method'] != "null"?$product['tax_method']:null,
        ];
        unset($product['form_unit']);
        if(isset($input['id'])){
            $product = $this->product->find($input['id']);
            $input['unit_deleted'] = explode(',',$input['unit_deleted']);
            foreach((array)$input['unit_deleted'] as $key => $value){
                if ($value) $this->productUnit->delete($value);
            }
            foreach($data_units as $key => $value){
                if(isset($value['id'])){
                    $productUnit =  $this->productUnit->find($value['id']);
                    if($productUnit['sale_price'] != $value['sale_price']){
                        if($catalogDetail=\DB::table('catalog_details')->where('product_id', $product->id)->where('unit_id', $value['unit_id'])){
                            $catalogDetail->update(['sale_price' => $value['sale_price']]);
                        }
                    }
                    $this->savePriceHistory($value, $product->id, $productUnit, true);
                    $this->productUnit->update($value['id'],$value);
                    unset($data_units[$key]);
                }
            }
            // End update existing unit

            // Add new unit
            if(count($data_units)>0){
                foreach($data_units as $key => $value){
                    $this->savePriceHistory($value, $product->id);
                }
                $product->productUnit()->createMany($data_units);
            }
            // End add new unit

            // When product don't have unit the product will update to not active
            if(count($product->productUnit) == 0){
                $datas['is_active'] = false;
            }
            if($datas['type']==Product::TYPE_PACKAGE){
                $datas['is_track_inventory'] = false;
                $datas['is_track_expire']    = false;
            }else{
                $datas['is_track_inventory'] = true;
            }
            $product = $this->product->update($input['id'],$datas);
        }else{
            if(count($data_units) == 0){
                $datas['is_active'] = false;
            }
            if($datas['type']==Product::TYPE_PACKAGE){
                $datas['is_track_inventory'] = false;
                $datas['is_track_expire']    = false;
            }
            $product = $this->product->create($datas);
            foreach($data_units as $key => $value){
                $this->savePriceHistory($value, $product->id);
            }
            $product->productUnit()->createMany($data_units);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product has been created'
        ]);
    }

    public function validateGenalData($input) {
        $id = $input['id']??null;
        $rqId = $id?(",$id,id,deleted_at,NULL"):(",null,1,deleted_at,NULL");
        $validator = Validator::make($input, [
            'name_international' => 'required',
            'code'              => 'required|min:1|unique:products,code'.$rqId.'|unique:product_unit,code,null,1,deleted_at,NULL|unique:hampers,code,null,1,deleted_at,NULL|',
            'qty_reorder'       => !empty($input['type']) && $input['type'] == 2 ?'':'required|integer|min:1',
            'expire_alert'      => !empty($input['expire_alert']) && $input['expire_alert'] != 'null' ?'integer|min:1':'nullable',
            'max_purchase'      => !empty($input['max_purchase']) && $input['max_purchase'] != 'null' ?'integer|min:1':'nullable',
            'form_unit'        => 'required',
            'group_unit_id'      => 'required',
            'form_unit'        => 'required',
            'composition'      => 'max:255',
            // 'tax'               => !empty($input['tax'])?'integer|min:0|max:100':'nullable',
        ],[
            'name_international.required' => 'Name International is required',
            'code.required'                => 'Code is required',
            'qty_reorder.required'       => 'Qty Reorder is required',
            'form_unit.required'        => 'Product need more than one unit',
            'group_unit_id.required'    => 'Group unit is required',
            'form_unit.required'        => 'Unit of sale required at least one',
        ]);

        $duplicate_code_in_request = in_array( $input['code'], collect(json_decode($input['form_unit'],true))->pluck('code')->toArray() );

        if($validator->fails() || $duplicate_code_in_request) {
            $validator = $validator->errors();
            if($duplicate_code_in_request){
                $validator->add('code', 'Code is duplicate with unit code');
            }
            return response()->json([
                'errors' => $validator,
            ]);
        }
    }

    public function validateFormUnit($input_unit,$id)
    {
        $input_unit = collect($input_unit)->map(function($unit){
            return collect($unit)->map(function($key){
                return empty($key) ? null : $key;
            });
        })->toArray();
        $data_unit=[];
        $exist_code = [];
        foreach ($input_unit as $key => $value) {

            $value = (array)$value;

            if($value['code'] !== null && !array_key_exists($value['code'], $exist_code)) {
                $exist_code[$value['code']] = true;
            }elseif(array_key_exists($value['code'], $exist_code)){
                $add_error = true;
            }
            $validator_unit = Validator::make($value, [
                'code'       => "nullable|sometimes|max:255|unique:product_unit,code,".($id?"$id,product_id":"null,1").",deleted_at,NULL|unique:hampers,code,null,1,deleted_at,NULL|unique:products,code,null,1,deleted_at,NULL",
                'unit_id'    => 'required',
                'uom_number' => 'required|integer|min:1',
                'sale_price' => 'required|numeric|min:0',
                'whole_sale_price' => $value['whole_sale_price']?'numeric|min:0':"|nullable",
            ]);
            if($validator_unit?->fails() || ($add_error??false)){
                isset($add_error)?$validator_unit->errors()->add('code', 'The code has already been taken'):null;
                $form_unit[$key] = $validator_unit->errors();
            }
            $data_unit[$key] = $value;
        }
        // End validate unit

        if((isset($form_unit)&&count($form_unit)>0)) {

            return [
                'success' => false,
                'errors_formUnit' => $form_unit??null
            ];
        }else {
            return [
                'success'=> true,
                'data_unit'=> $data_unit
            ];
        }
    }

    public function savePriceHistory($value, $product_id, $productUnit = [], $is_edit = false)
    {
        $p_history = [
            'productable_id'   => $product_id,
            'productable_type' => Product::class,
            'unit_id'          => $value['unit_id'],
            'uom_number'       => $value['uom_number'],
        ];

        if(($value['sale_price'] && $is_edit == false) || ($is_edit == true && $productUnit['sale_price'] != $value['sale_price'])){
            $p_history['unit_price'] = $value['sale_price'];
            $p_history['type'] = PriceHistory::RETAIL_PRICE;
            $this->priceHistory->create($p_history);
        }

        if(($value['whole_sale_price'] && $is_edit == false) || ($is_edit == true && $productUnit['whole_sale_price'] != $value['whole_sale_price'])){
            $p_history['unit_price'] = $value['whole_sale_price']?? 0;
            $p_history['type'] = PriceHistory::WHOLE_SALE_PRICE;
            $this->priceHistory->create($p_history);
        }
    }
}
