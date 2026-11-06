<?php

use App\Models\Currency;
use Fwta\Currency\database\Models\ExchangeRate;
return [
	/*
    |--------------------------------------------------------------------------
    | Currency Model
    |--------------------------------------------------------------------------
    |
    | Here you may specify the model of currency in order to manage your currency
    | data.
    |
    */
	'model_currency'=> Currency::class,
    'model_exchange_rate'=> ExchangeRate::class,
    'decimals'            => 2,
];