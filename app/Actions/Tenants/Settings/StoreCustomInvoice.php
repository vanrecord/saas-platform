<?php
namespace App\Actions\Tenants\Settings;

use Illuminate\Support\Facades\Request;
use App\Repositories\CustomInvoices\CustomInvoiceRepository;

class StoreCustomInvoice
{

    public function __construct(){
        $this->customInvoice = App(CustomInvoiceRepository::class);
    }
    public function store($request){
        $id = $request->id??null;
        $rqId = $id?(",$id,id,form_type,$request->form_type,deleted_at,NULL"):(",null,1,form_type,$request->form_type,deleted_at,NULL");
        $request->validate([
            'name'    => 'required|unique:custom_invoices,name'.$rqId,
            'order' => ($request->order != '1' || $request->is_default == false) ?'required|numeric|min:1|unique:custom_invoices,order'.$rqId:''
        ]);
        if($request->form_type == 1){
            $request['data'] = json_encode(
                $request->only('logo', 'shop_name', 'header_noted', 'header_continue_page', 'counter','phone','cashier','customer','customer_phone','discount', 'name_en', 'exchange_rate',
                'total_discount','tax','date','footer_noted','address','txt_header_noted', 'txt_phone', 'txt_footer_noted', 'header_color'
                )
            );
        }else{
            $request['invoice_type'] = null;
            $request['data'] = json_encode(
                $request->only('logo', 'header_noted', 'counter', 'phone','customer','vat_no', 'discount', 'name_en', 'exchange_rate',
                'total_discount','tax','footer_noted','address','txt_header_noted', 'txt_phone', 'txt_footer_noted','txt_vat_number'
                )
            );
        }
        if($request->id){
            if($request->is_default == false){
                $countDefault = $this->customInvoice->query()->where('form_type', $request->form_type)->where('is_default', true)->where('id', '!=', $request->id)->count();
                if($countDefault <= 0){
                    return ['success'=> false, 'msg' => 'Custom invoice can not is_default null!'];
                }
            }
            $this->updateIsDefault($request->form_type, $request->is_default, $request->id);
            $this->updateOrder($request->form_type, $request->is_default,$request->id);

            $this->customInvoice->update($request->id, $request->all());
            $msg = "Custom invoice updated successfully";
        }else{
            if($this->customInvoice->query()->where('form_type', $request->form_type)->where('is_default', true)->count() <= 0){
                $request['order'] = 1;
                $request['is_default'] = true;
            }
            if($request->is_default == true){
                $this->customInvoice->query()->where('form_type', $request->form_type)->where('is_default', true)->update(['order' => null, 'is_default' => false]);
            }
            $this->customInvoice->create($request->all());
            $msg = "Custom invoice created successfully";
        }
        return $msg;

    }

    public function updateIsDefault($form_type,$is_default, $id='')
    {
        if($id){
            $this->customInvoice->query()->where('id', '!=', $id)
            ->where('form_type', $form_type)
            ->where('is_default', $is_default)->update(['is_default' => false]);
        }
    }
    public function updateOrder($form_type,$is_default,$id='')
    {
        if($id){
            $this->customInvoice->query()->where('id', '!=', $id)
            ->where('form_type', $form_type)
            ->where('order', 1)
            ->where('is_default', false)
            ->update(['order' => null]);
        }
    }
}
