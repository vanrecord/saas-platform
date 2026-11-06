<?php
namespace App\Actions\Tenants\Products;

use App\Models\Product;
use App\Repositories\Products\ProductRepository;
use App\Repositories\Shrinkages\ShrinkageDetailRepository;
use App\Repositories\Shrinkages\ShrinkageRepository;
use App\Supports\Status;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class StoreShrinkage 
{
    protected $shrinkage;
    protected $shrinkageDetail;

    public function __construct()
    {
        $this->shrinkage = app(ShrinkageRepository::class);
        $this->shrinkageDetail = app(ShrinkageDetailRepository::class);
        $this->product = app(ProductRepository::class);
    }

    public function store($request){
        if($request->Adjusted_by && is_array($request->Adjusted_by)){
            $request->Adjusted_by = $request->Adjusted_by['id'];
        }
        $validator = Validator:: make($request->all(), [
            'Adjusted_by' => 'required',
            'reason' => 'required',
            // 'total_amount' => 'required',
            'Adjusted_date' => 'required',
        ],[
            'Adjusted_date.required' => 'Date field is required',
            'Adjusted_date.date' => 'Date field is invalid',
        ]);
        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()
            ], 200);
        }
        if($request->id){
            $shrinkage = $this->shrinkage->find($request->id);
            $code = $shrinkage->code;
            if($request->status == Status::SUBMITTED['value']&& $code == null ) {
                $code = $this->shrinkage->max('code')?$this->shrinkage->max('code')+1:1;
            }elseif($request->status == Status::APPROVED['value']){
                $dataTransaction = $shrinkage->shrinkageDetail->map(function($item){
                    return [
                        'warehouse_id' => $item->warehouse_id,
                        'productable_id' => $item->productable_id,
                        'productable_type' => $item->productable_type,
                        'quantity' => $item->quantity*$item->uom_number,
                        'cost' => $item->cost,
                        'expired_at' => $item->expired_at?Carbon::createFromFormat('d/m/Y', $item->expired_at)->format('Y-m-d'):null,
                        'transactionable_type' => 'App\Models\ShrinkageDetail',
                        'transactionable_id' => $item->id,
                    ];
                })->toArray();
                
                $stock_out = \StockTransaction::stockOut($dataTransaction);
                
                if(isset($stock_out['success']) && $stock_out['success'] == false){
                    $productName = $this->product->whereIn('id', $stock_out['data'])->findAll('name_international');
                    $msg = [];
                    foreach($productName as $item) {
                        $msg[$item->name_international][] = $item->name_international.' is out of stock.';
                    }
                    return response()->json([
                            'error' => $msg
                        ], 200);
                }
            }
            
            $shrinkage = $this->shrinkage->update($request->id,[
                'code' => $code,
                'requested_at'=> $request->Adjusted_date,
                'requested_by'=> $request->Adjusted_by,
                'type'=> $request->reason,
                'total_amount'=> $request->total_amount,
                'status'=> $request->status,
                'description'=> $request->description,
                'comment'=> $request->comment??null,
            ]);
        }else{
            $shrinkage = $this->shrinkage->create([
                'requested_at'=> $request->Adjusted_date,
                'requested_by'=> $request->Adjusted_by,
                'type'=> $request->reason,
                'total_amount'=> $request->total_amount,
                'status'=> Status::DRAFT['value'],
                'description'=> $request->description,
            ]);
        }
        return response()->json($shrinkage);
    }

    public function storeItem($request){
        $validator = Validator:: make($request->all(), [
            'product_id'    => 'required',
            'unit'          => 'required',
            'mvt_qty'       => 'required',
            'warehouses'    => 'required',
            // 'expire_date'   => 'required|date_format:d/m/Y',
        ]);
        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()
            ], 200);
        }
        if($request->id){
            $shrinkageDetail = $this->shrinkageDetail->update($request->id,[
                'unit_id'           => $request->unit['id'],
                'uom_number'        => $request->unit['pivot']['uom_number'],
                'quantity'          => $request->mvt_qty,
                'cost'              => $request->is_unit_cost,
                'warehouse_id'      => $request->warehouses,
                'expired_at'        => $request->expire_date?$request->expire_date['expired_at']:null,
                'total_amount'      => $request->mvt_qty*$request->unit['pivot']['uom_number']*$request->is_unit_cost,
            ]);
        }else{
            $shrinkageDetail = $this->shrinkageDetail->create([
                'shrinkage_id'      => $request->shrinkage_id,
                'productable_id'    => $request->product_id['id'],
                'productable_type'  => Product::class,
                'unit_id'           => $request->unit['id'],
                'uom_number'        => $request->unit['pivot']['uom_number'],
                'quantity'          => $request->mvt_qty,
                'cost'              => $request->is_unit_cost,
                'warehouse_id'      => $request->warehouses,
                'expired_at'        => $request->expire_date?$request->expire_date['expired_at']:null,
                'total_amount'      => $request->mvt_qty*$request->unit['pivot']['uom_number']*$request->is_unit_cost,
            ]);
        }
        $total_amount = $shrinkageDetail->shrinkage->getTotalCost();
        $this->shrinkage->update($shrinkageDetail->shrinkage_id,['total_amount'=>$total_amount]);
        $data = [
                'shrinkage_detail' => $shrinkageDetail,
                'total_amount' => $total_amount
            ];
        return response()->json($data);
    }

    public function cancel($request){
        if($request->is_create){
            $this->shrinkage->forceDelete($request->id);
        }else{
            $shrinkage = $this->shrinkage->find($request->id);
            foreach($shrinkage->shrinkageDetail as $detail){
                $this->shrinkageDetail->forceDelete($detail->id);
            }
            // $shrinkage->shrinkageDetail()->forceDelete();
            $shrinkage->shrinkageDetail()->createMany(
                $request->data['shrinkage_detail']
            );
            $shrinkage->refresh();
            $total_amount = $shrinkage->getTotalCost();
            $shrinkage = $this->shrinkage->update($request->id,['total_amount'=>$total_amount]);
        }
    }
        
}