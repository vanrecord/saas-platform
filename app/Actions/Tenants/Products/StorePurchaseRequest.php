<?php
namespace App\Actions\Tenants\Products;

use Illuminate\Support\Facades\Validator;
use App\Repositories\PurchaseRequest\PurchaseRequestRepository;
use App\Repositories\PurchaseRequest\PurchaseRequestDetailRepository;
use App\Supports\Status;
use Carbon\Carbon;

class StorePurchaseRequest 
{
    protected $purchaseRequest;
    protected $purchaseRequestDetail;

    public function __construct()
    {
        $this->purchaseRequest = App(PurchaseRequestRepository::class);
        $this->purchaseRequestDetail = App(PurchaseRequestDetailRepository::class);
    }

    public function store($request){
        $validator = Validator:: make($request->all(), [
            'requested_by' => 'required',
            'approved_by' => 'required',
            'date' => 'required|date_format:d/m/Y',
        ],[
            'date.required' => 'Date field is required',
            'date.date' => 'Date field is invalid',
        ]);
        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors()
            ], 200);
        }
        
        if($request->id){
            $purchaseRequest = $this->purchaseRequest->find($request->id);
            $code = $purchaseRequest->code;
            if($request->status == Status::SUBMITTED['value']&& $code == null ) {
                $code = $this->purchaseRequest->max('code')?$this->purchaseRequest->max('code')+1:1;
            }
            $purchaseRequest = $this->purchaseRequest->update($request->id,[
                'code' => $code,
                'requested_by' => $request->requested_by,
                'approved_by' => $request->approved_by,
                'description' => $request->description,
                'requested_at' => Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d'),
                'comment' => $request->comment,
                'status' => $request->status,
            ]);
        }else{
            $purchaseRequest = $this->purchaseRequest->create([
                'currency' => $request->currency,
                'requested_by' => $request->requested_by['id']??$request->requested_by,
                'approved_by' => $request->approved_by,
                'requested_at' => Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d'),
                'status' => $request->status,
                'description' => $request->description,
                'comment' => $request->comment,
            ]);
        }
        return response()->json($purchaseRequest);
    }

    public function storeItem($request){
        $validator = Validator:: make($request->all(), [
            'product_id' => 'required|unique:purchase_request_details,product_id,'.($request->id ?? 'null').',id,purchase_request_id,'.($request->purchase_request_id ?? 'null').',deleted_at,NULL',
            'unit_id' => 'required',
            'quantity' => 'required|integer|min:1',
            'uom_number' => 'required|integer',
        ],[
            'product_id.required' => 'Please select product',
            'product_id.unique' => 'Product already added',
        ]);
        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()],200);
        }
        $data = array_merge(
            $request->only(['product_id','purchase_request_id','unit_id','uom_number']),
            ['quantity' => $request->quantity*$request->uom_number]
        );
        $purchaseRequestDetail = $request->id
            ? $this->purchaseRequestDetail->update($request->id,$data)
            : $this->purchaseRequestDetail->create($data);
        return response()->json($purchaseRequestDetail->load(['unit', 'product.productUnit.unit']));
    }

    public function cancel($request){
        if($request->is_create){
            $this->purchaseRequest->delete($request->id);
        }else{
            $purchaseRequest = $this->purchaseRequest;
            $purchaseRequest->update($request->data['id'],[
                'code' => $request->data['code'],
                'requested_at' => Carbon::createFromFormat('d/m/Y H:i', $request->data['requested_at'])->format('Y-m-d'),
                'requested_by' => $request->data['requested_by'],
                'created_by' => $request->data['created_by'],
                'updated_by' => $request->data['updated_by'],
                'approved_by' => $request->data['approved_by'],
                'approved_at' => $request->data['approved_at'],
                'rejected_by' => $request->data['rejected_by'],
                'rejected_at' => $request->data['rejected_at'],
                'comment' => $request->data['comment'],
                'status' => $request->data['status'],
                'created_at' => $request->data['created_at'],
                'updated_at' => $request->data['updated_at'],
                'deleted_at' => $request->data['deleted_at'],
                'description' => $request->data['description'],
            ]);
            $purchaseRequest->find($request->data['id'])->purchaseRequestDetails()->delete();
            $purchaseRequest->find($request->data['id'])->purchaseRequestDetails()->createMany(
                $request->data['purchase_request_details']
            );
        }
    }
        
}