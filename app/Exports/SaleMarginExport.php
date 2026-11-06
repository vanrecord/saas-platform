<?php

namespace App\Exports;

use App\Supports\Status;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Repositories\Orders\OrderDetailRepository;

class SaleMarginExport implements FromView
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
        $product_id = null;
        if(!empty($this->ids)){
            $product_id = explode(',',$this->ids);
        }
        $sale_margins = $this->orderDetail->getOrderMarginItem($this->date_from, $this->date_to, $this->time_from, $this->time_to, $product_id);
        $sale_items = $this->orderDetail->getOrderMargin(null, $this->date_from,$this->date_to, $this->time_from, $this->time_to); 
        return view('report.sale_margin.excel', [
            'sale_margins' => $sale_margins,
            'sale_items' => $sale_items,
            'currency' => \Currency::all()
        ]);
    }
}