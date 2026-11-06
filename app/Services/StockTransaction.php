<?php
namespace App\Services;

use App\Models\PurchaseOrderDetail;
use App\Repositories\Stocks\StockRepository;
use App\Repositories\Stocks\StockTransactionRepository;
use App\Repositories\TrackingCost\TrackingCostRepository;
use App\Repositories\Products\ProductRepository;
use Carbon\Carbon;
use App\Models\Product;

class StockTransaction{

    protected $stock;
    protected $stockTransaction;
    protected $product;
    protected $trackingCost;

    public function __construct()
    {
        $this->stock = app(StockRepository::class);
        $this->stockTransaction = app(StockTransactionRepository::class);
        $this->product = app(ProductRepository::class);
        $this->trackingCost = app(TrackingCostRepository::class);
    }

	public function StockIn(Array $data){
        // Example:
                // $arr[] = [
                    // 'warehouse_id' => 1,
                    // 'product_id' => 1,
                    // 'quantity' => 20,
                    // 'unit_price' => 100,
                    // 'expired_info' => '{"2022-05-11":10,"2025-05-01":10,null:10}',
                    // 'transactionable_type' => 'App\Models\PurchaseOrder',
                    // 'transactionable_id' => 1,
                // ];
        $check = null;
        \DB::beginTransaction();
        try {
            collect($data)->lazy()->each(function ($value) use (&$check){
                if(isset($value['expired_info'])){
                    $expired_info = json_decode($value['expired_info'], true);
                    collect($expired_info)->lazy()->each(function ($expired_quantity, $expired_date) use ($value, &$check){
                        if($expired_date==null){
                            $check = $this->stockTransaction->create([
                                'warehouse_id'          => $value['warehouse_id'],
                                'productable_id'        => $value['productable_id'],
                                'productable_type'      => $value['productable_type'],
                                'balance'               => $expired_quantity,
                                'unit_price'            => $value['unit_price'],
                                'transactionable_type'  => $value['transactionable_type'],
                                'transactionable_id'    => $value['transactionable_id'],
                                'status'                => 1,           // 1 = Stock In
                            ]);
                        }else{
                            $check = $this->stockTransaction->create([
                                'warehouse_id'          => $value['warehouse_id'],
                                'productable_id'        => $value['productable_id'],
                                'productable_type'      => $value['productable_type'],
                                'balance'               => $expired_quantity,
                                'unit_price'            => $value['unit_price'],
                                'expired_at'            => $expired_date,
                                'transactionable_type'  => $value['transactionable_type'],
                                'transactionable_id'    => $value['transactionable_id'],
                                'status'                => 1,           // 1 = Stock In
                            ]);
                        }
                    });
                }else{
                    $check = $this->stockTransaction->create([
                        'warehouse_id'          => $value['warehouse_id'],
                        'productable_id'        => $value['productable_id'],
                        'productable_type'      => $value['productable_type'],
                        'balance'               => $value['quantity']??null,
                        'unit_price'            => $value['unit_price'],
                        'transactionable_type'  => $value['transactionable_type'],
                        'transactionable_id'    => $value['transactionable_id'],
                        'status'                => 1,           // 1 = Stock In
                    ]);
                }
                // $check = $this->stockTransaction->create([
                //     'warehouse_id'          => $value['warehouse_id'],
                //     'product_id'            => $value['product_id'],
                //     'balance'               => $value['quantity']??null,
                //     'unit_price'            => $value['unit_price'],
                //     'expired_info'          => $value['expired_info']??null,
                //     'transactionable_type'  => $value['transactionable_type'],
                //     'transactionable_id'    => $value['transactionable_id'],
                //     'status'                => 1,           // 1 = Stock In
                // ]);
            });
            \DB::commit();
            if($check){
                return true;
            }else{
                return false;
            }
        }catch(\Exception $e) {
            \DB::rollback();
            info("StockIn: ".$e->getMessage());
            return false;
        }
        return true;
    }

    public function StockOut(Array $data){
        $check = [];
        $fails = [];
        $this->stockTransaction->beginTransaction();
        $this->stock->beginTransaction();
        try {
            collect($data)->lazy()->each(function($value) use (&$check, &$fails){
                if(isset($value['expired_info'])){
                    $expired_info = json_decode($value['expired_info'], true);
                    collect($expired_info)->lazy()->each(function($expired_quantity, $expired_date) use ($value, &$check){
                        if($expired_date==null){
                            $check[] = $this->stockTransaction->create([
                                'warehouse_id'          => $value['warehouse_id'],
                                'productable_id'        => $value['productable_id'],
                                'productable_type'      => $value['productable_type'],
                                'balance'               => $expired_quantity,
                                'unit_price'            => $value['unit_price'],
                                'transactionable_type'  => $value['transactionable_type'],
                                'transactionable_id'    => $value['transactionable_id'],
                                'status'                => 2,           // 2 = Stock Out
                                'type'                  => $value['type']??null,  // type of hamper = 1
                            ]);
                        }else{
                            $check[] = $this->stockTransaction->create([
                                'warehouse_id'          => $value['warehouse_id'],
                                'productable_id'        => $value['productable_id'],
                                'productable_type'      => $value['productable_type'],
                                'balance'               => $expired_quantity,
                                'unit_price'            => $value['unit_price'],
                                'expired_at'            => $expired_date,
                                'transactionable_type'  => $value['transactionable_type'],
                                'transactionable_id'    => $value['transactionable_id'],
                                'status'                => 2,           // 2 = Stock Out
                                'type'                  => $value['type']??null,
                            ]);
                        }
                    });
                }else{
                    $create = $this->stockTransaction->create([
                        'warehouse_id'          => $value['warehouse_id'],
                        'productable_id'        => $value['productable_id'],
                        'productable_type'      => $value['productable_type'],
                        'balance'               => $value['quantity']??null,
                        'unit_price'            => $value['unit_price']??null,
                        'transactionable_type'  => $value['transactionable_type'],
                        'transactionable_id'    => $value['transactionable_id'],
                        'expired_at'             => $value['expired_at']??null,
                        'status'                => 2,           // 2 = Stock Out
                        'type'                  => $value['type']??null,
                    ]);
                    $check[] = $create;
                    if($create == false){
                        $fails[] = $value['productable_id'];
                    
                    }
                }
            });
            if($check && count($fails)==0){
                $this->stockTransaction->commit();
                $this->stock->commit();
                return $check;
            }else{
                $this->stockTransaction->rollBack();
                $this->stock->rollBack();
                return ['success' => false, 'data' => $fails];
            }
        }catch(\Exception $e) {
            $this->stockTransaction->rollBack();
            $this->stock->rollBack();
            info("StockOut: ".$e->getMessage());
            return false;
        }
        return true;
    }

    public function trackingCost($data){
        foreach($data as $item){
            
            // $purchaseOrder = app(PurchaseOrderDetail::class)->where(
            //     'product_id', '=', $item['productable_id']
            // )->selectRaw('
            //         sum(
            //             CASE WHEN discount>0 AND discount_type = 1 THEN (received_quantity*unit_price)-((received_quantity*unit_price)*discount/100)  
            //             WHEN discount>0 AND discount_type = 2 
            //                 THEN (received_quantity*unit_price)-discount
            //             ELSE (received_quantity*unit_price) END
            //             ) as total_cost, 
            //         sum(received_quantity) as total_quantity')->first();
            
            // $initialize = $this->product->query()
            //             ->where('id', $item['productable_id'])
            //             ->selectRaw('initialize_cost*initialize_qty as total_cost, initialize_qty as total_quantity')
            //             ->first();

            // $cost = ($purchaseOrder->total_cost+$initialize->total_cost)/($purchaseOrder->total_quantity+$initialize->total_quantity);

            $tracking = $this->trackingCost->where('productable_id', $item['productable_id'])->orderBy('id', 'desc')->findFirst();
            
            $stock = $this->stock->query()->where([
                    ['productable_id', $item['productable_id']],
                    ['productable_type', Product::class],
                ])
                ->selectRaw('SUM(balance_available) as balance_available')->first();
            $balance = $stock->balance_available-$item['quantity'];
                
            $lastest_cost = $tracking?(float)$tracking->cost_average:0;

            $purchase_price = $item['unit_price']*$item['quantity'];
            if($item['discount_type']==2 && !empty($item['discount'])){
                $purchase_price = $purchase_price*(1-($item['discount']/100));
            }else if($item['discount_type'] == 1 && !empty($item['discount'])){
                $purchase_price = $purchase_price-$item['discount'];
            }

            //((last_cost*balance)+(purchase_price*purchase_qty))/(balance+purchase_qty)
            $cost = (($lastest_cost*$balance)+($purchase_price))/($stock->balance_available);
            
            if(empty($tracking) || ($tracking && $tracking->cost_average != round($cost,4,PHP_ROUND_HALF_UP))){
                $this->trackingCost->create([
                        'cost_average'     => round($cost,4,PHP_ROUND_HALF_UP),
                        'productable_id'   => $item['productable_id'],
                        'productable_type' => Product::class,
                        'tracked_at'       => \Carbon\Carbon::now()->format('d/m/Y')
                    ]);

                $this->stock->where([
                    ['productable_id', $item['productable_id']],
                    ['productable_type', Product::class],
                ])->findAll()->each(function($stock) use ($cost){
                    $stock->cost = round($cost,4,PHP_ROUND_HALF_UP);
                    $stock->save();
                });
            }

            
        }
        
    }
}