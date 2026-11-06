<?php
return [
    'additional' => [
        'additional_type' =>[
            1 => 'Fixed amount',
            2 => 'Rate %',
        ] ,
        'additional_sign' =>[
            1 => 'Increase',
            2 => 'Decrease',
        ] ,
    ], 

    'discount_type' => [
        1 => 'Fixed',
        2 => '%',
    ],

    'setting' => [
        'pos_stock',
        'change_coefficient',
        'change_to_currency',
        'print_invice_item_per_page',
        'prefix_purchase_request',
        'prefix_purchase_order',
        'prefix_invoice',
    ],
    'setting_prefix' => [
        'prefix_purchase_request' => 'Purchase Request',
        'prefix_purchase_order'   => 'Purchase Order',
        'prefix_invoice'          => 'Invoice',
    ],

    'movement_type' => [
        'App\Models\PurchaseOrderDetail' => 'Purchase',
       'App\Models\Product' => 'Initialize',
       'App\Models\OrderDetail' => 'Sale',
       'App\Models\ShrinkageDetail' => 'Shrinkage',
    ],
    'per_page_option' => [10, 15, 30, 50, 100],
];