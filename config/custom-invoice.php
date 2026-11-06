<?php

return [

    'sale_invoice' => [
        'visibility' => [
            'Header' => [
                'logo' => 'Logo',
                'shop_name' => 'Name',
                'header_noted' =>'Noted',
                'header_continue_page' => 'Header continue page'
            ],
    
            'Body' => [
                // 'counter' => 'Counter',
                'cashier' => 'Cashier',
                'phone' => 'Phone',
                'customer' => 'Customer',
                'customer_phone' => 'Customer Phone', 
                'discount' => 'Discount col'
            ],
            'Hide Name En' => [
                'name_en' => 'Name En'
            ],
            'Footer' => [
                'exchange_rate' => 'Exchange rate',
                'total_discount' => 'Total discount',
                'tax' => 'Tax',
                'date' => 'Date',
                'footer_noted' => 'Noted',
                'address' => 'Address',
            ],
        ],
        'text' => [
            'txt_header_noted' => 'Header noted',
            'txt_phone' => 'Phone',
            'txt_footer_noted' => 'Footer noted' 
    
        ],
        'design' => [
            'Header color' => [
                'header_color' => [
                    'blue-darker',
                    'black',
                    'primary-darker',
                    'pink-darker',
                    'green-darker',
                    'purple-darker',
                    'olive-darker',
                ]
            ],
        ],
    ],
    'pos_invoice' => [
        'visibility' => [
            'Header' => [
                'logo' => 'Logo',
                'header_noted' =>'Noted'
            ],
    
            'Body' => [
                // 'counter' => 'Counter',
                'phone' => 'Phone',
                'customer' => 'Customer',
                'vat_no' => 'VAT No',
                'discount' => 'Discount col'
            ],

            'Hide Name En' => [
                'name_en' => 'Name En'
            ],

            'Footer' => [
                'exchange_rate' => 'Exchange rate',
                'total_discount' => 'Total discount',
                'tax' => 'Tax',
                'footer_noted' => 'Noted',
                'address' => 'Address',
                // 'invoice_barcode' => 'Invoice Barcode'
            ],
        ],
        'text' => [
            'txt_header_noted' => 'Header noted',
            'txt_vat_number' => 'Vat Number',
            'txt_phone' => 'Phone',
            'txt_footer_noted' => 'Footer noted' 
    
        ], 
    ],
    'form_type' => [
        1 => 'Sale Invoice',
        2 => 'POS Invoice'
    ],
];