<?php

namespace App\QueryBuilder;

use App\Models\Product;
use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class SortByReport implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        
        if($property === "p_name_en"){
            $query->orderBy(
                Product::selectRaw("BTRIM(products.name_international, ' ') as name_international")
                    ->whereColumn('products.id', 'stocks.productable_id'),
                $direction
            )->orderBy('expired_at', 'asc');
        }elseif($property === "p_code"){
            $query->orderBy(
                Product::select('code')
                    ->whereColumn('products.id', 'stocks.productable_id'),
                $direction
            )->orderBy('expired_at', 'asc');
        }
    }
}