<?php

namespace App\QueryBuilder;

use App\Models\Product;
use App\Models\PurchaseRequest;
use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class SortByColumn implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        if($property === "pr_code"){
            $query->orderBy(
                PurchaseRequest::select('code')
                    ->whereColumn('purchase_requests.id', 'purchase_orders.purchase_request_id'),
                $direction
            );
        }elseif($property === "pr_requested_at"){
            $query->orderBy(
                PurchaseRequest::select('requested_at')
                    ->whereColumn('purchase_requests.id', 'purchase_orders.purchase_request_id'),
                $direction
            );
        }elseif($property === "pr_requested_by"){
            $query->orderBy(
                PurchaseRequest::select('requested_by')
                    ->whereColumn('purchase_requests.id', 'purchase_orders.purchase_request_id'),
                $direction
            );
        }

        if($property === "p_code"){
            $query->orderBy(
                Product::select('code')
                    ->whereColumn('products.id', 'stocks.productable_id'),
                $direction
            );
        }elseif($property === "p_name_en"){
            $query->orderBy(
                Product::select('name_international')
                    ->whereColumn('products.id', 'stocks.productable_id'),
                $direction
            );
        }

        if($property === "st_p_code"){
            $query->orderBy(
                Product::select('code')
                    ->whereColumn('products.id', 'stock_transactions.productable_id'),
                $direction
            )->orderBy('transactionable_type', 'desc');
        }elseif($property === "st_p_name_en"){
            $query->orderBy(
                Product::select('name_international')
                    ->whereColumn('products.id', 'stock_transactions.productable_id'),
                $direction
            )->orderBy('transactionable_type', 'desc');
        }
    }
}