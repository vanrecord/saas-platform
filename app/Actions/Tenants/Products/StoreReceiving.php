<?php
namespace App\Actions\Tenants\Products;

use App\Models\PriceHistory;
use App\Models\Product;
use App\Supports\Status;
use App\Repositories\PriceHistory\PriceHistoryRepository;
use App\Repositories\PurchaseOrder\PurchaseOrderRepository;
use App\Repositories\ProductUnit\ProductUnitRepository;
use App\Repositories\PurchaseOrder\PurchaseOrderDetailRepository;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class StoreReceiving
{
    protected $purchaseOrder;
    protected $productUnit;
    protected $purchaseOrderDetail;
    protected $priceHistory;

    public function __construct(){
        $this->purchaseOrder = app(PurchaseOrderRepository::class);
        $this->productUnit = app(ProductUnitRepository::class);
        $this->purchaseOrderDetail      = app(PurchaseOrderDetailRepository::class);
        $this->priceHistory = app(PriceHistoryRepository::class);
    }

    public function cancel($request){
        $PO = $this->purchaseOrder->findBy('id',$request->data['id']);
        $PO->update([
            'code'                      => $request->data['code'],
            'purchase_request_id'       => $request->data['purchase_request_id'],
            'supplier_id'               => $request->data['supplier_id'],
            'status'                    => $request->data['status'],
            'currency'                  => $request->data['currency'],
            'exchange_rate'             => $request->data['exchange_rate'],
            'tax'                       => $request->data['tax'],
            'discount'                  => $request->data['discount'],
            'description'               => $request->data['description'],
            'requested_by'              => $request->data['requested_by']['id'],
            'requested_at'              => $request->data['requested_at'],
            'approved_by'               => $request->data['approved_by'],
            'approved_at'               => $request->data['approved_at'],
            'rejected_by'               => $request->data['rejected_by'],
            'rejected_at'               => $request->data['rejected_at'],
            'comment'                   => $request->data['comment'],
            'credit_term'               => $request->data['credit_term'],
            'purchase_sub_total_amount' => $request->data['purchase_sub_total_amount'],
            'received_by'               => $request->data['received_by'],
            'received_at'               => $request->data['received_at'],
            'delivery_note'             => $request->data['delivery_note'],
            'receive_sub_total_amount'  => $request->data['receive_sub_total_amount'],
            'payment_status'            => $request->data['payment_status'],
            'total_amount'              => $request->data['total_amount'],
        ]);
        $PO = $this->purchaseOrder;
        $PO->find($request->data['id'])->purchaseOrderDetails()->forceDelete();
        $pur_order_details = collect($request->data['purchase_order_details'])->map(function ($item){
            $item['received_quantity'] = $item['received_quantity'] * $item['uom_number'];
            $item['unit_price'] = $item['unit_price'] / $item['uom_number'];
            return $item;
        });
        $PO->find($request->data['id'])->purchaseOrderDetails()->createMany($pur_order_details);
        // $PO->find($request->data['id'])->purchaseOrderDetails()->createMany(
        //     $request->data['purchase_order_details']
        // );
        $PO->forgetCache();
        return response()->json(['success' => true]);
    }

    public function store($request){
        $request->validate([
            'DN_PO' => [function($attribute, $value, $fail) use ($request){
                if(is_array($value)){
                    foreach(collect($value)->filter(fn ($item) => !is_array($item)) as $file){
                        $validator = Validator::make(
                            [
                                'file' => $file,
                            ],
                            [
                                'file' => 'required|mimes:pdf,jpeg,jpg,png|max:5120',
                            ]
                        );
                        if($validator->fails()){
                            $fail('File should be pdf, jpeg, jpg, png and less than 5MB');
                        }
                    }
                }
            }],
        ]);
        $request->validate([
            'received_by' => 'required',
            'received_at' => 'required',
        ],[
            'received_by.required' => 'Receiver is required',
            'received_at.required' => 'Receive date is required',
        ]);

        $this->purchaseOrder->where('id', $request->id)->findFirst()->deleteMedia(false,true);
        $purchaseOrder = $this->purchaseOrder->update($request->id,[
            'status'                => $request->status,
            'received_by'           => $request->received_by,
            'received_at'           => $request->received_at,
            'receive_description'   => $request->receive_description,
            'DN_PO'                 => $request->DN_PO,
        ]);
        if($purchaseOrder->status == Status::COMPLETED['value']){
            $po_detail = $purchaseOrder->purchaseOrderDetails
                                        ->whereNull('is_free');
            foreach($po_detail as $value){
                $this->addPriceHistory($value);
                
            }
        }
        return Redirect::route('receiving-product.index');
    }

    public function save($request)
    {   
        $files = $request->DN_PO ? $request->DN_PO : [];
        if($request->DN_PO_OLD){
            foreach($request->DN_PO_OLD as $old_file){
                array_push($files, json_decode($old_file, true));
            }
        }
        $request->DN_PO = $files;
        $validator = Validator::make($request->all(),[
            'DN_PO' => [
                function ($attribute, $value, $fail) {
                    if(is_array($value)){
                        foreach(collect($value)->filter(fn ($item) => is_object($item)) as $file){
                            $validator = Validator::make(
                                [
                                    'file' => $file,
                                ],
                                [
                                    'file' => 'required|mimes:pdf,jpeg,jpg,png|max:5120',
                                ]
                            );
                            if($validator->fails()){
                                $fail('File should be pdf, jpeg, jpg, png and less than 5MB');
                            }
                        }
                    }
                
            },],
            'received_by' => 'required',
            'received_at' => 'required',
            'supplier_id' => 'required',
            'discount'    => $request->discount ? 'numeric|min:0|max:100': 'nullable',
            'tax'         => $request->tax ? 'numeric|min:0|max:100': 'nullable',
            'status'      => 'integer',
        ],[
            'received_by.required' => 'Receiver is required.',
            'received_at.required' => 'Receive date is required.',
            'supplier_id.required' => 'Supplier is required.',
            
        ]);
        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()
            ], 200);
        }
        if($request->id){
            $this->purchaseOrder->where('id', $request->id)->findFirst()->deleteMedia(false,true);
            $purchaseOrder = $this->purchaseOrder->update($request->id,[
                'supplier_id'         => $request->supplier_id ,
                'status'              => $request->status,
                'received_by'         => $request->received_by,
                'received_at'         => $request->received_at,
                'discount'            => $request->discount != 'null' ? $request->discount : null,
                'tax'                 => $request->tax != 'null' ? $request->tax : null,
                'description'         => $request->description != 'null' ? $request->description: null,
                'DN_PO'               => $request->DN_PO,
                'status'              => $request->status,
                'is_receiving_direct' => $request->is_receiving_direct,
            ]);
            if($purchaseOrder->status == Status::COMPLETED['value']){
                $po_detail = $purchaseOrder->purchaseOrderDetails
                                            ->whereNull('is_free');
                foreach($po_detail as $value){
                    $this->addPriceHistory($value);
                }
            }
            return Redirect::route('receiving-product.index');
        }else{
            $purchaseOrder = $this->purchaseOrder->create([
                'supplier_id'         => $request->supplier_id,
                'credit_term'         => $request->credit_term != 'null' ?$request->credit_term : null,
                'requested_by'        => $request->requested_by,
                'requested_at'        => $request->requested_at,
                'received_by'         => $request->received_by,
                'received_at'         => $request->received_at,
                'discount'            => $request->discount != 'null' ? $request->discount : null,
                'currency'            => $request->currency,
                'tax'                 => $request->tax != 'null' ? $request->tax : null,
                'description'         => $request->description != 'null' ? $request->description : null,
                'DN_PO'               => $request->DN_PO,
                'is_receiving_direct' => $request->is_receiving_direct
            ]);
            return response()->json($purchaseOrder);
        }

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

    public function addFreeItem($request){
        $validator = Validator::make($request->all(),[
            'product_id'            => ['required',
                function($attribute, $value, $fail) use ($request) {
                    $purchase_order_detail = $this->purchaseOrderDetail->query()->where([
                        ['purchase_order_id', '=', $request->purchase_order_id],
                        ['product_id', '=', $value],
                        ['is_free', '=', true]
                    ])->when($request->id, function($query) use ($request){
                        $query->where('id','!=',$request->id);
                    })->first();
                    if($purchase_order_detail){
                        return $fail('This product is already added as free item');
                    }
                }
            ],
            'received_quantity'     => 'required',
            'unit_id'               => 'required',
        ],[
            'received_quantity.required' => 'Please enter received quantity',
        ]);
        $arr = [];
        if($request->expired_info){
            foreach($request->expired_info as $key => $value){
                $date = Carbon::createFromFormat('d/m/Y', $value['date'])->format('Y-m-d');
                $arr[$date] = $value['quantity']*$this->productUnit->query()->where([
                    ['product_id','=',$request->product_id],
                    ['unit_id','=' ,$request->unit_id]
                ])->first()->uom_number;
            }
        }
        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()
            ], 200);
        }
        if($request->id){
            $purchase_order_detail = $this->purchaseOrderDetail->update($request->id,[
                'purchase_order_id' => $request->purchase_order_id,
                'product_id'        => $request->product_id,
                'unit_id'           => $request->unit_id,
                'received_quantity' => $request->received_quantity,
                'is_free'           => true,
                'expired_info'      => count($arr)>0?json_encode($arr,true):null,
                'warehouse_id'      => $request->warehouse_id,
            ]);
        }else{
            $purchase_order_detail = $this->purchaseOrderDetail->create([
                'purchase_order_id' => $request->purchase_order_id,
                'product_id'        => $request->product_id,
                'unit_id'           => $request->unit_id,
                'received_quantity' => $request->received_quantity,
                'is_free'           => true,
                'expired_info'      => count($arr)>0?json_encode($arr,true):null,
                'warehouse_id'      => $request->warehouse_id,
            ]);
        }
        return response()->json($purchase_order_detail);
    }

    public function addPriceHistory($item)
    {
        $history = $this->priceHistory
                        ->where('productable_id', $item->product_id)
                        ->where('unit_id', $item->unit_id)
                        ->where('type', PriceHistory::PURCHASE)
                        ->latest()->first();
        if(empty($history) || (isset($history) && $history['unit_price'] != $item['unit_price'])){
            $p_history = [
                'productable_id'   => $item->product_id,
                'productable_type' => Product::class,
                'unit_id'          => $item->unit_id,
                'uom_number'       => $item->uom_number,
                'unit_price'       => $item->unit_price,
                'type'             => PriceHistory::PURCHASE
            ];
            $this->priceHistory->create($p_history);
        }
    }
}