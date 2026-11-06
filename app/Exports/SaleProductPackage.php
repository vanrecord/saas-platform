<?php

namespace App\Exports;

use App\Supports\Status;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Repositories\Orders\OrderDetailRepository;

class SaleProductPackage implements FromView
{

    protected $orderDetail;
    protected $date_from;
    protected $date_to;
    protected $time_from;
    protected $time_to;
    protected $ids;

    public function __construct($date_from, $date_to, $time_from, $time_to, $ids)
    {
        $this->date_from = $date_from;
        $this->date_to = $date_to;
        $this->time_from = $time_from;
        $this->time_to = $time_to;
        $this->ids = $ids;
        $this->orderDetail = App(OrderDetailRepository::class);
    }

    public function view(): View
    {
        $sale_margins = $this->orderDetail 
                                        ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
                                        ->where('orders.payment_status', '=', Status::PAID['value'])
                                        ->whereDate('invoiced_at', '>=', $this->date_from)
                                        ->whereDate('invoiced_at', '<=', $this->date_to)
                                        ->whereTime('invoiced_at', '>=', $this->time_from)
                                        ->whereTime('invoiced_at', '<=', $this->time_to)
                                        ->groupBy('order_details.orderable_id', 'order_details.orderable_type','currency')
                                        ->selectRaw("
                                            SUM(order_details.quantity) AS total_qty,
                                            SUM(order_details.cost*order_details.uom_number) AS total_cost_unit,
                                            SUM(order_details.cost*(order_details.uom_number*order_details.quantity)) as total_cost,
                                            SUM(order_details.unit_price) AS total_unit_price,
                                            SUM(order_details.price_before_tax) AS total_amount,
                                            SUM(case when order_details.discount_type= 1 then order_details.discount else 0 end) AS discount, 
                                            SUM(case when order_details.discount_type= 2 then order_details.discount else 0 end) AS discount_amount,
                                            SUM(order_details.sub_total) AS after_discount,
                                            SUM(order_details.product_tax) AS item_tax,
                                            SUM(order_details.product_tax) AS item_tax_amount,
                                            SUM(order_details.total) AS grand_total,
                                            orderable_id,
                                            orderable_type,
                                            currency
                                        ")->filterProductPackage();
        $sale_items =  $this->orderDetail 
                       ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
                        ->where('orders.payment_status', '=', Status::PAID['value'])
                        ->whereDate('invoiced_at', '>=', $this->date_from)
                        ->whereDate('invoiced_at', '<=', $this->date_to)
                        ->whereTime('invoiced_at', '>=', $this->time_from)
                        ->whereTime('invoiced_at', '<=', $this->time_to)
                        ->selectRaw("
                            orders.code,
                            orders.invoiced_at,
                            orders.currency,
                            order_details.*
                        ")->with('unit')->filterProductPackage();
        if(!empty($this->ids)){
            $id = explode(',',$this->ids);
            $sale_margins = $sale_margins->whereIn('orderable_id', $id);
            $sale_items = $sale_items->whereIn('orderable_id', $id);
        }
        return view('report.sale_product_package.excel', [
            'sale_margins' => $sale_margins->with('orderable')->get(),
            'sale_items' => $sale_items->get(),
            'currency' => \Currency::all()
        ]);
    }
}