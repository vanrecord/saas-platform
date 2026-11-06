<?php
namespace App\Actions\Tenants\Products;

use Illuminate\Support\Facades\Validator;
use App\Repositories\PurchaseOrder\PurchaseOrderRepository;
use App\Repositories\PurchaseOrder\PurchaseOrderDetailRepository;
use App\Repositories\PurchaseRequest\PurchaseRequestRepository;
use App\Supports\Status;
use Carbon\Carbon;

class StorePurchaseOrder 
{
    protected $purchaseOrder;
    protected $purchaseOrderDetail;
    protected $purchaseRequest;

    public function __construct()
    {
        $this->purchaseOrder = App(PurchaseOrderRepository::class);
        $this->purchaseOrderDetail = App(PurchaseOrderDetailRepository::class);
        $this->purchaseRequest = App(PurchaseRequestRepository::class);
    }

    public function store($request){

        // validate request
        $data = $request->all();
        $data['discount'] = (float) $data['discount'];
        $data['tax'] = (float) $data['tax'];
        is_array($data['requested_by'])?$data['requested_by'] = $data['requested_by']['id']:null;
        is_array($data['approved_by'])?$data['approved_by'] = $data['approved_by']['id']:null;
        $validator = Validator::make($data,[
            'requested_by'  => 'required|exists:users,id',
            'approved_by'   => 'required|exists:users,id',
            'currency'      => 'required|exists:currencies,id',
            'supplier_id'   => 'required',
            'discount'      => 'numeric|min:0|max:100',
            'tax'           => 'numeric|min:0|max:100',
            'status'        => 'integer',  
            'requested_at'  => 'required',
        ],[
            'currency.required'     => 'Please create currency first and set atleast one currency to default.',
            'currency.exists'       => 'Please create currency first and set atleast one currency to default.',
            'requested_at.required' => 'Please select requested date.',
            'supplier_id.required'  => 'Please select supplier.',
        ]);
        // find inactive product . if found, return error
        $inactive_product = $this->purchaseOrder->with(['purchaseOrderDetails' => function ($query){
            $query->with(['product' => function ($query){
                $query->where('is_active',true)->select('id');
            }])->select('id','purchase_order_id','product_id');
        }])->find($request->id,['id'])?->purchaseOrderDetails->first(fn ($item) => $item->product == null );
        // end validate request

        if($validator->fails() || $inactive_product){
            return response()->json([
                'error' => $inactive_product 
                    ? [['Inactive product found.']] 
                    : $validator->errors()
            ], 200);
        }

        $data = $request->only(['supplier_id','credit_term','discount','tax','requested_by','approved_by','requested_at','currency','description','status','comment']);
        $data['requested_by'] = $data['requested_by']['id'] ?? $data['requested_by'];
        
        if($request->id && $request->status == Status::SUBMITTED['value']){
            $error = $this->purchaseOrder
                ->find($request['id'])->purchaseOrderDetails
                ->first(fn($item) => $item->unit_price == 0);
            if($error){
                return response()->json([
                    'error' => [['Please update unit price of all items.']]
                ], 200);
            }
        }
        $purchaseOrder = $request->id
            ? $this->purchaseOrder->update($request->id,$data)
            : $this->purchaseOrder->create($data);
        return response()->json($purchaseOrder);
    }

    public function update($request){

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:purchase_orders,id',
            'requested_by' => 'required|exists:users,id',
            'approved_by' => 'required|exists:users,id',
            'currency' => 'required|exists:currencies,id',
            'discount' => 'min:0|max:100',
            'tax' => 'min:0|max:100',
            'credit_term' => 'integer',
            'requested_at' => 'required',
        ],[
            'currency.required' => 'Please select currency.',
            'requested_at.required' => 'Please select requested date.',
            'requested_by.required' => 'Please select requester.',
            'approved_by.required' => 'Please select approver.',
        ]);
        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()
            ], 200);
        }
        $purchaseOrder = $this->purchaseOrder->update($request->id,$request->only(['requested_by','approved_by','currency','discount','tax','supplier_id','credit_term','requested_at','purchase_request_id']));
        return response()->json($purchaseOrder);
    }

    public function storeItem($request)
    {
        $validator = Validator::make($request->all(), [
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'product_id'        => 'required|unique:purchase_order_details,product_id,'.($request->id ?? 'null').',id,purchase_order_id,'.$request->purchase_order_id.',deleted_at,NULL',
            'quantity'          => 'required|integer|min:1',
            'unit_price'        => 'required|numeric|min:0',
            'unit_id'           => 'required|exists:units,id',
            'discount'          => $request->discount ? 'numeric|min:0|max:100' : 'nullable',
            'tax'               => $request->tax ? 'numeric|min:0|max:100' : 'nullable',
        ],[
            'product_id.required'   => 'Please select product.',
            'product_id.unique'     => 'Product already added.',
            'unit_id.required'      => 'Please select unit',
            'unit_id.exists'        => 'Please select unit',
        ]);

        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()
            ], 200);
        }
        
        $data = $request->only(['purchase_order_id','product_id','unit_id','unit_price','discount','discount_type','tax','quantity']);
        $data['purchase_quantity'] = $data['quantity'];
        unset($data['quantity']);

        $purchaseOrderDetail = $request->id
            ? $this->purchaseOrderDetail->update($request->id,$data)
            : $this->purchaseOrderDetail->create($data);

        return response()->json($purchaseOrderDetail->load(['unit', 'product.productUnit.unit']));
    }

    public function getPurchaseRequestItem($request){

        $data = $request->all();
        isset($data['requested_by']['id'])?$data['requested_by'] = $data['requested_by']['id']:null;
        isset($data['approved_by']['id'])?$data['approved_by'] = $data['approved_by']['id']:null;
        $data['discount']   = (float) $data['discount'] ??= 0;
        $data['tax']        = (float) $data['tax'] ??= 0;

        $validator = Validator::make($data, [
            'requested_by'  => 'required|exists:users,id',
            'approved_by'   => 'required|exists:users,id',
            'currency'      => 'required|exists:currencies,id',
            'discount'      => 'numeric|min:0|max:100',
            'tax'           => 'numeric|min:0|max:100',
            'supplier_id'   => 'required',
        ],[
            'currency.required' => 'Please create currency first and set atleast one currency to default.',
            'currency.exists'   => 'Please create currency first and set atleast one currency to default.',
        ]);

        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()
            ], 200);
        }

        $data = $request->only(['purchase_request_id','supplier_id','credit_term','requested_by','approved_by','requested_at','discount','tax','currency']);
        $data['requested_by'] = $data['requested_by']['id'] ?? $data['requested_by'];

        $purchaseOrder = $request->id
            ? $this->purchaseOrder->update($request->id,$data)
            : $this->purchaseOrder->create($data);

        if($purchaseOrder->purchaseOrderDetails->count()>0){
            $purchaseOrder->purchaseOrderDetails()->delete();
        }

        $purchase_order_details = $this->purchaseRequest
            ->findBy('id',$request->purchase_request_id)->purchaseRequestDetails
            ->filter(fn($item)=>!$item->is_used)->map(fn($item)=>[
                'purchase_order_id' => $purchaseOrder->id,
                'product_id'        => $item->product_id,
                'unit_price'        => 0,
                'discount'          => $purchaseOrder->discount,
                'tax'               => $purchaseOrder->tax,
                'unit_id'           => $item->unit_id,
                'purchase_quantity' => $item->quantity/$item->uom_number,
            ]);
        
        $purchase_order_details->each(fn($item)=>$this->purchaseOrderDetail->create($item));
        return response()->json($purchaseOrder);
    }

    public function cancel($request){
        if($request->is_create){
            $this->purchaseOrder->delete($request->id);
        }else{
            $this->purchaseOrder->update($request->data['id'],collect($request->data)->except('purchase_request','purchase_order_details')->toArray());
            $this->purchaseOrder->findBy('id',$request->data['id'])->purchaseOrderDetails()->forceDelete();
            collect($request->data['purchase_order_details'])->each(function ($item) {
                $this->purchaseOrderDetail->create($item);
            });
        }
    }
    
    public function updateStatus($request){
        $validator = Validator::make($request->all(), [
            'id' => [
                function ($attribute, $value, $fail) use ($request) {
                    if(!$this->purchaseOrder->query()->where('id','=',$request->id)->whereIn('status', [3,7,8,9])->exists()){
                        $fail('Purchase order is not in status to update status.');
                    }
                },
            ],
            'status' => 'required|in:3,7,8,9',
        ]);
        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()
            ], 200);
        }
        $purchaseOrder = $this->purchaseOrder->update($request->id,[
            'status' => $request->status,
        ]);
        return response()->json($purchaseOrder);
    }

    public function receivedItem($request){
        $purchase_order_detail = $this->purchaseOrderDetail->with('purchaseOrder')->findBy('id',$request->id);
        $validator = Validator::make($request->all(),[
            'expired_info' => [
                function ($attribute, $value, $fail) use ($purchase_order_detail) {
                    $count = 0; 
                    $arr = collect($value)->map(function ($item) use (&$count) {
                        if($item['quantity'] <= 0)
                            return 'Quantity Input can not be negative or 0 !';
                        if(!is_numeric($item['quantity']))
                            return 'Quantity must be a number.';
                        $count += $item['quantity'];
                    })->filter(fn($item)=>$item)->toArray();
                    $total_purchase_quantity = $purchase_order_detail->purchase_quantity * $purchase_order_detail->uom_number;
                    if($purchase_order_detail->purchaseOrder->is_receiving_direct == true && $count <> $total_purchase_quantity)
                        $arr[] = 'Received quantity must be equal to '.$total_purchase_quantity.'.';
                    elseif(!$purchase_order_detail->is_free && $count > $total_purchase_quantity)
                        $arr[] = 'Received quantity must be less than or equal to '.$total_purchase_quantity.'.';
                    if(count($arr) > 0)
                        $fail($arr);
                },
            ],
            'qty' => [
                function ($attribute, $value, $fail) use ($purchase_order_detail){
                    $total_purchase_quantity = $purchase_order_detail->purchase_quantity * $purchase_order_detail->uom_number;
                    $arr = [];
                    if($purchase_order_detail->purchaseOrder->is_receiving_direct == true && $total_purchase_quantity <> $value){
                        $arr[] = 'Received quantity must be equal to '.$total_purchase_quantity.'.';
                    }elseif(!$purchase_order_detail->is_free && $value > $total_purchase_quantity){
                        $arr[] = 'Received quantity must be less than or equal to '.$total_purchase_quantity.'.';
                    }
                    if(count($arr) > 0)
                        $fail($arr);
                },
            ]

        ]);
        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()
            ], 200);
        }

        $data = $request->expired_info 
            ? [ 'expired_info' => json_encode(collect($request->expired_info)->mapWithKeys(fn ($item) => [ Carbon::createFromFormat('d/m/Y', $item['date'])->format('Y-m-d') => $item['quantity'] ] ),true), 'warehouse_id' => $request->warehouse_id ] 
            : [ 'received_quantity' => $request->qty, 'warehouse_id' => $request->warehouse_id, 'unit_price' => $request->unit_price];

        $this->purchaseOrderDetail->update($request->id,array_merge($data,[
            'skip_model_automation' => true,
        ]));

        return response()->json([
            'success' => 'Successfully updated.'
        ]);
    }
}