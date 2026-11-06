<?php
namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class StockTransactionFacade extends Facade{
    protected static function getFacadeAccessor() { return 'stock-transaction'; }
}