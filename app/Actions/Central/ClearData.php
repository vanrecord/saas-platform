<?php

namespace App\Actions\Central;

use App\Models\Tenant;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Request;

class ClearData
{
    private function configTables(){
        return [
            'clear-stock' => [
                'stock_transactions',
                'purchase_payments',
                'purchase_request_details',
                'purchase_requests',
                'purchase_order_details',
                'purchase_orders',
                'transfer_payments',
                'stock_transfer_expire',
                'stock_transfer_details',
                'stock_transfers',
                'request_transfer_details',
                'request_transfers',
                'shrinkage_details',
                'shrinkages',
                'stocks',
                'tracking_costs',
                'payments',
                'orders',
                'order_expires',
                'order_details',
            ],
            'clear-product' => [
                'hamper_details',
                'hampers',
                'catalog_details',
                'catalogs',
                'customer_group_items',
                'product_supplier_group',
                'sale_template_details',
                'sale_templates',
                'product_unit',
                'products',
                'group_unit_pivot',
                'units',
                'group_units',
            ],
            'clear-setting' => [
                'opening_balances',
                'media',
                'customer_groups',
                'customers',
                'settings',
                'bank_accounts',
                'banks',
                'exchange_rates',
                'currencies',
                'warehouses',
                'lab_supplier',
                'labs',
                'manufactures',
                'categories',
                'item_levels',
                'compositions',
                'custom_invoices',
                'suppliers',
                'supplier_groups',
                'payment_methods',
            ]
        ];
    }

    public function clearData($tenant_id, $type){
        try {
            $result = false;
            \Tenancy::find($tenant_id)->run(function () use ($type, &$result) {
                
                switch($type) {
                    case('stock'):
                        $result = $this->clearStock();
                        break;
                    case('product'):
                        $result = $this->clearStock();
                        $result = $this->clearProduct();
                        break;
                    case('all'):
                        $result = $this->clearStock();
                        $result = $result?$this->clearProduct():false;
                        $result = $result?$this->clearSetting():false;
                        break;
                    default:
                        $result = false;    
                }
            });
            return $result;
            
        } catch(Exception $e) {
            return false;
        }
    }

    public function clearStock()
    {
        try {

            collect($this->configTables()['clear-stock'])->each(function($table){
                \DB::statement("TRUNCATE {$table} restart identity CASCADE;");
            });
            
            return true;
            
        } catch(Exception $e) {
            return false;
        }
    }

    public function clearProduct()
    {
        try {
            collect($this->configTables()['clear-product'])->each(function($table){
                \DB::statement("TRUNCATE {$table} restart identity CASCADE;");
            });

            return true;
            
        } catch(Exception $e) {
            return false;
        }
    }

    public function clearSetting()
    {
        try {
            collect($this->configTables()['clear-setting'])->each(function($table){
                \DB::statement("TRUNCATE {$table} restart identity CASCADE;");
            });

            return true;
            
        } catch(Exception $e) {
            return false;
        }
    }
}
