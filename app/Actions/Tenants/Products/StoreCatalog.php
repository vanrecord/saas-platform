<?php
namespace App\Actions\Tenants\Products;

use Illuminate\Support\Facades\Validator;
use App\Repositories\Catalogs\CatalogRepository;
use App\Repositories\Catalogs\CatalogDetailRepository;

class StoreCatalog 
{
    protected $catalog;
    protected $catalogDetail;

    public function __construct()
    {
        $this->catalog = App(CatalogRepository::class);
        $this->catalogDetail = App(CatalogDetailRepository::class);
    }

    public function store($request){
        $validator = Validator:: make($request->all(), [
            'name_international' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()
            ], 200);
        }
        $datas = [
            'name_international' => $request->name_international,
            'name_native' => $request->name_native,
            'is_active' => $request->is_active,
            'created_by' => auth()->user()->id,
            'updated_by' => auth()->user()->id,
        ];
        if($request->id){
            $catalog = $this->catalog->update($request->id,$datas);
        }else{
            $catalog = $this->catalog->create($datas);
        }
        return response()->json($catalog);
    }

    public function storeItem($request){
        $id = !empty($request->id)?$request->id:'';
        $validateUnit = $id?(",$id,id,product_id,$request->product_id,catalog_id,$request->catalog_id"):(",null,1,product_id,$request->product_id,catalog_id,$request->catalog_id");
        $validator = Validator:: make($request->all(), [
            'product_id' => 'required',
            'unit_id' => 'required|unique:catalog_details,unit_id'.$validateUnit,
            'sale_price' => 'required|numeric|min:0',
        ],[
            'unit_id.unique' => 'Product unit already added',
        ]);
        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()
            ], 200);
        }
        if($request->id){
            $catalogDetail = $this->catalogDetail->update($request->id,[
                'product_id' => $request->product_id,
                'catalog_id' => $request->catalog_id,
                'unit_id' => $request->unit_id,
                'sale_price' => $request->sale_price,
            ]);
        }else{
            $catalogDetail = $this->catalogDetail->create([
                'product_id' => $request->product_id,
                'catalog_id' => $request->catalog_id,
                'unit_id' => $request->unit_id,
                'sale_price' => $request->sale_price
            ]);
        }
        return response()->json($catalogDetail->load(['unit', 'product.productUnit.unit', 'product.productUnit']));
    }
        
}