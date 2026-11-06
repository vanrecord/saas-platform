<?php

namespace App\Imports;

use App\Events\ProductImportSuccess;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\User;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\GroupUnits\GroupUnitRepository;
use App\Repositories\Lab\LabRepository;
use App\Repositories\Manufactures\ManufactureRepository;
use App\Repositories\ProductUnit\ProductUnitRepository;
use App\Repositories\Products\ProductRepository;
use App\Repositories\Stocks\StockRepository;
use App\Repositories\Stocks\StockTransactionRepository;
use App\Repositories\Units\UnitRepository;
use App\Repositories\Warehouses\WarehouseRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
// use Maatwebsite\Excel\Concerns\Importable;
// use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
// use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\AfterImport;

class ProductStockImport implements ToCollection,WithHeadingRow, WithChunkReading
{
    // use Importable, RegistersEventListeners;
    protected $warehouses   = null;
    protected $arrImported  = [];
    protected $user;
    protected $tenantId;
    protected $result       =[];
    protected $category     = null;
    protected $lab = null;
    protected $manufacture = null;
    protected $group_unit = null;
    protected $unit = null;
    protected $unit_code = null;
    public function __construct($tenant_id, User $user)
    {
        $this->warehouses = App(WarehouseRepository::class)->findAll();
        $this->user       = $user;
        $this->tenantId    = $tenant_id;
        $this->category    = App(CategoryRepository::class)->findAll();
        $this->lab         = App(LabRepository::class)->findAll();
        $this->manufacture = App(ManufactureRepository::class)->findAll();
        $this->group_unit = App(GroupUnitRepository::class)->findAll();
        $this->unit = App(UnitRepository::class)->with('groupUnits')->findAll();
        $this->unit_code = App(ProductUnitRepository::class)->findAll()->pluck('code')->toArray();
    }
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        $arrCode     = $rows->pluck('code')->toArray();
        $arrStock    = App(ProductRepository::class)
                        ->leftJoin('stocks', function($join)use($arrCode){
                            $join->on('products.id', '=', 'stocks.productable_id')
                            ->where('productable_type', Product::class);
                        })
                        ->whereIn('products.code',$arrCode)
                        ->pluck('stocks.productable_id','code')->toArray();
        $msg =[];
            // dd($rows);
        foreach($rows as $item) {
            if(!isset($item['code']) || empty($item['code'])){
                if(isset($item['no'])&&$item['no']){
                    $msg[] =['no'=>$item['no']??'', 'code' => $item['code']??'', 'msg'=>'The code field is required.'];
                }
            }
            else if(in_array($item['code'], array_keys($arrStock)) && $arrStock[$item['code']]!=null){
                $msg[] = ['no'=>$item['no']??'', 'code' => $item['code']??'', 'msg'=>'The product already has stock.'];
                continue;
            }
            else if(in_array($item['code'], array_keys($arrStock))){
                //insert stock
                $product = App(ProductRepository::class)->findBy('code', $item['code']);
                $case1 = $this->insertStock($product, $item);
                $msg[] = $case1;
                
            }
            else if(in_array($item['code'], array_keys($this->arrImported))){
                //insert stock
                $product = $this->arrImported[$item['code']];
                $case2 = $this->insertStock($product, $item);
                $msg[] = $case2;
            }
            else if($item['code'] != null) {
                //Create product and stock
                $case3 = $create = $this->createProduct($item);
                $msg[] = $case3;
            }
        }
        
        $this->result = $msg;
        // ProductImportSuccess::dispatch($this->tenantId,$this->user,$this->result);
    }
  
    public function createProduct($item) {
        
        if(!isset($item['is_unit']) || empty($item['is_unit'])) {
            return ['no'=>$item['no']??'', 'code' => $item['code']??'', 'msg'=>'The is unit field is required.'];
        }

        // Category section
        $item['category_id'] = null;
        if(isset($item['category']) && !empty($item['category'])){
            $category = $this->category->filter(function ($cat) use($item) {
                    return strtolower($cat->name_international) === strtolower($item['category']) || strtolower($cat->name_native) === strtolower($item['category']);
                })->first();
            if(empty($category)) {
                $category = App(CategoryRepository::class)->create(['name_international'=>$item['category'], 'name_native'=>$item['category'], 'is_active'=> true]);
                $this->category->push($category);
            }
            $item['category_id'] = $category->id;
        }
        // End category
        
        // Lab and manufacture section
        $item['lab_id'] = null;
        if(isset($item['lab']) && !empty($item['lab'])){
            $manufacture_id = null;
            $labs = $this->lab->filter(function ($lab) use($item) {
                    return strtolower($lab->name) === strtolower($item['lab']);
                })->first();
            if(empty($labs)) {
                if(isset($item['manufacture']) && !empty($item['manufacture'])){
                    $manufacture = $this->manufacture->filter(function ($manu) use($item) {
                        return strtolower($manu->name) === strtolower($item['manufacture']);
                    })->first();
                    if(empty($manufacture)) {
                        $manufacture = App(ManufactureRepository::class)->create(['name' => $item['manufacture'], 'is_active'=>true]);
                        $this->manufacture->push($manufacture);
                    }
                    $manufacture_id = $manufacture->id;
                }

                $labs = App(LabRepository::class)->create(['name'=>$item['lab'], 'manufacture_id'=>$manufacture_id, 'is_active'=> true]);
                $this->lab->push($labs);
            }
            $item['lab_id'] = $labs->id;
        }
        // // End lab and manufacture

        // // Group Unit section
        $item['group_unit_id'] = null;
        if(isset($item['group_unit']) && !empty($item['group_unit'])){
            $group_unit = $this->group_unit->filter(function ($gu) use($item) {
                return strtolower($gu->name_international) === strtolower($item['group_unit']) || strtolower($gu->name_native) === strtolower($item['group_unit']);
            })->first();
            if(empty($group_unit)){
                $group_unit = App(GroupUnitRepository::class)->create(['name_native'=>$item['group_unit'], 'name_international'=>$item['group_unit'], 'is_active'=>true]);
                $this->group_unit->push($group_unit);
            }
            $item['group_unit_id'] = $group_unit->id;
        }
        // // End group unit

        $data['code']               = $item['code'];
        $data['name_international'] = $item['name_en']??null;
        $data['name_native']        = $item['name_native']??null;
        
        $data['category_id']      = $item['category_id'];
        $data['lab_id']           = $item['lab_id'];
        $data['group_unit_id'] = $item['group_unit_id'];

        $data['composition']       = $item['composition']??null;
        $data['qty_reorder']       = $item['qty_reorder']??null;
        $data['expire_alert']      = $item['expire_alert']??null;
        $data['created_by']        = $this->user->id;


        $product = App(ProductRepository::class)->create($data);
        $this->arrImported[$item['code']] = $product;
        $this->createDefaultUnit($product, $item);
        $this->createUnit($product, $item);
        $this->insertStock($product, $item);
    }

    public function createDefaultUnit($product, $item) {
        $unit = $this->unit->filter(function ($subUnit) use($item) {
                return strtolower($subUnit->name_international) === strtolower($item['is_unit']) || strtolower($subUnit->name_native) === strtolower($item['is_unit']);
            })->first();

        if(empty($unit)){
            $unit = App(UnitRepository::class)->create(['name_native'=>$item['is_unit'],'name_international'=>$item['is_unit'], 'is_active'=>true]);
            $unit->groupUnits()->attach($item['group_unit_id']);
            $this->unit->push($unit);
        }else {
            $group_unit = $unit->groupUnits->where('id',$item['group_unit_id'])->first();
            if(empty($group_unit)) {
                $unit->groupUnits()->attach($item['group_unit_id']);
            }
        }

        $dataUnitDefault['code'] = null;
        if(!in_array($item['is_unit_barcode'],$this->unit_code)) {
            $dataUnitDefault['code']             = $item['is_unit_barcode'];
            array_push($this->unit_code, $item['is_unit_barcode']);
        }
        $dataUnitDefault['unit_id']          = $unit->id;
        $dataUnitDefault['product_id']       = $product->id;
        $dataUnitDefault['uom_number']       = 1;
        $dataUnitDefault['is_default']       = true;
        $dataUnitDefault['sale_price']       = $item['is_unit_retail_price']??null;
        $dataUnitDefault['whole_sale_price'] = $item['is_unit_wholesale_price']??null;
        $defaultUnit = $product->productUnit()->create($dataUnitDefault);
    }

    public function createUnit($product, $item) {
        $dataUnit =[];
        for($i=1; $i<=3; $i++) {
            $key = 'level'.$i;
            if(isset($item['unit_'.$key]) && !empty($item['unit_'.$key])) 
            {
                $unit = $this->unit->filter(function ($subUnit) use($item,$key) {
                        return strtolower($subUnit->name_international) === strtolower($item['unit_'.$key]) || strtolower($subUnit->name_native) === strtolower($item['unit_'.$key]);
                    })->first();
                if(empty($unit)){
                    $unit = App(UnitRepository::class)->create(['name_native'=>$item['unit_'.$key],'name_international'=>$item['unit_'.$key], 'is_active'=>true]);
                    $unit->groupUnits()->attach($item['group_unit_id']);
                    $this->unit->push($unit);
                }else {
                    $group_unit = $unit->groupUnits->where('id',$item['group_unit_id'])->first();
                    if(empty($group_unit)) {
                        $unit->groupUnits()->attach($item['group_unit_id']);
                    }
                }
                $code = null;
                if(isset($item[$key.'_barcode']) && !in_array($item[$key.'_barcode'],$this->unit_code)){
                    $code = $item[$key.'_barcode'];
                    array_push($this->unit_code, $item[$key.'_barcode']);
                }
                
                $dataUnit[$key]['product_id']       = $product->id;
                $dataUnit[$key]['code']             = $code;
                $dataUnit[$key]['unit_id']          = $unit->id;
                $dataUnit[$key]['uom_number']       = $item[$key.'_uom']??0;
                $dataUnit[$key]['is_default']       = false;
                $dataUnit[$key]['sale_price']       = $item[$key.'_retail_price']??null;
                $dataUnit[$key]['whole_sale_price'] = $item[$key.'_wholesale_price']??null;
            }
            
        } 
        $productUnit = $product->productUnit()->createMany($dataUnit);  
    }

    public function insertStock($product, $item) {
        if((isset($item['balance_expire']) && $item['balance_expire'] > 0) && (isset($item['stock'])&&!empty($item['stock']))){
            $warehouse = explode('/',$item['stock']);
            $stock = $this->warehouses->where('name',$warehouse[0])->first();
            $subStock = null;
            if(empty($stock)) {
                return ['no'=>$item['no']??'', 'code' => $item['code']??'', 'msg'=>'Warehouse does not exist.'];
            }
            else if(isset($warehouse[1])) {
                $stock = $this->warehouses->where('name',trim($warehouse[1]))->first();
                if(empty($stock)) {
                    return ['no'=>$item['no']??'', 'code' => $item['code']??'', 'msg'=>'Sub-WH/Location does not exist.'];
                }
            }
            // $insert_stock = App(StockRepository::class)->create([
            //         'warehouse_id'      => $stock->id,
            //         'balance'           => $item['balance_expire'],
            //         'balance_available' => $item['balance_expire'],
            //         'cost'              => $item['cost_is_unit'],
            //         'expired_at'        => $item['expire']?Carbon::createFromFormat('d/m/Y',$item['expire']):Carbon::now()->subYears(10),
            //         'productable_id' => $product->id,
            //         'productable_type' => Product::class
            //     ]);
            $expire = null;
            if($item['expire'] !== null && is_numeric($item['expire'])){
                $expire = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['expire']);
            }else if($item['expire'] == null){
                Carbon::now()->addYears(10)->format('Y-m-d');
            }else if($item['expire'] != null){
                $expire = Carbon::createFromFormat('d/m/Y',$item['expire'])->format('Y-m-d');
            }
            // dd($expire);
            $stockTransaction = App(StockTransactionRepository::class)->create([
                'balance' => $item['balance_expire'],
                'warehouse_id'=> $stock->id,
                'status'=> StockTransaction::STOCK_IN,
                'cost_average'=> $item['cost_is_unit'],
                // 'expired_at'=> $item['expire']?Carbon::createFromFormat('d/m/Y',$item['expire'])->format('Y-m-d'):Carbon::now()->addYears(10)->format('Y-m-d'),
                'expired_at'=> $expire,
                'productable_id'=> $product->id,
                'productable_type'=> Product::class
            ]);

        }
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getResult(): array
    {
        return $this->result;
    }
}
