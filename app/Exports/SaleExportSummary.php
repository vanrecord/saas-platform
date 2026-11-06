<?php

namespace App\Exports;

use App\Supports\Status;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Repositories\Orders\OrderRepository;

class SaleExportSummary implements FromView
{

    protected $order;
    protected $date_from;
    protected $date_to;
    protected $time_from;
    protected $time_to;

    public function __construct($date_from, $date_to, $time_from, $time_to)
    {
        $this->date_from = $date_from;
        $this->date_to = $date_to;
        $this->time_from = $time_from;
        $this->time_to = $time_to;
        $this->order = App(OrderRepository::class);
    }

    public function view(): View
    {
        $sale_summary = $this->order->getOrderSummary($this->date_from,$this->date_to,$this->time_from,$this->time_to);
        // $sale_summary = $this->order
        //                             ->leftJoin('order_details', 'order_details.order_id', '=', 'orders.id')
        //                             ->whereDate('invoiced_at', '>=', $this->date_from)
        //                             ->whereDate('invoiced_at', '<=', $this->date_to)
        //                             ->whereTime('invoiced_at', '>=', $this->time_from)
        //                             ->whereTime('invoiced_at', '<=', $this->time_to)
        //                             ->groupBy("date", "orders.currency")
        //                             ->select(\DB::raw('DATE(invoiced_at) AS date'))
        //                             ->selectRaw("
        //                                 SUM(order_details.cost*(order_details.uom_number*order_details.quantity)) AS total_cost,
        //                                 SUM(order_details.unit_price*order_details.quantity) AS total_amount,
        //                                 SUM(CASE WHEN orders.discount_type = 1 THEN ((orders.total * orders.discount)/100)
        //                                             ELSE orders.discount
        //                                         END) AS order_disc,
        //                                 SUM(orders.total - orders.sub_total) AS order_tax,
        //                                 SUM(orders.sub_total) AS sub_total,
        //                                 SUM(orders.total) AS grand_total,
        //                                 orders.currency
        //                             ")->orderBy('date', 'asc')->get();

        $invoices = $this->order->getOrder(null,$this->date_from,$this->date_to,$this->time_from,$this->time_to);
        // $invoices = $this->order
        //                 ->leftJoin('order_details', 'order_details.order_id', '=', 'orders.id')
        //                 ->whereDate('invoiced_at', '>=', $this->date_from)
        //                 ->whereDate('invoiced_at', '<=', $this->date_to)
        //                 ->whereTime('invoiced_at', '>=', $this->time_from)
        //                 ->whereTime('invoiced_at', '<=', $this->time_to)
        //                 ->groupBy('order_details.order_id', 'orders.id')
        //                 ->select(\DB::raw('DATE(invoiced_at) AS date'))
        //                 ->selectRaw("
        //                     SUM(order_details.cost*(order_details.uom_number*order_details.quantity)) AS total_cost,
        //                     SUM(order_details.unit_price*order_details.quantity) AS total_amount,
        //                     CASE WHEN orders.discount_type = 1 THEN ((orders.total * orders.discount)/100)
        //                                 ELSE orders.discount
        //                             END AS order_disc,
        //                     (orders.total - orders.sub_total) AS order_tax,
        //                     orders.sub_total AS sub_total,
        //                     orders.total AS grand_total,
        //                     orders.*
        //                 ")->orderBy('date', 'asc')->get();

        return view('report.sale.sale_detail', [
            'sale_summary' => $sale_summary,
            'invoices' => $invoices,
            'currency' => \Currency::all(),
            'is_sale_detail' => false
        ]);

        return view('report.sale.sale_summary', [
            'sale_summary' => $sale_summary,
            'invoices' => $invoices,
            'currency' => \Currency::all()
        ]);
    }
}