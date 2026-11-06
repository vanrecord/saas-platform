<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Repositories\VillageRepository;
use App\Models\Village;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class VillageController extends Controller
{
    public function __construct()
    {
        $this->village = App(VillageRepository::class);
    }

    public function index()
    {
        $this->authorize(app("request")->route()->getName(), Village::class);
        $villages = $this->village->getDataTable();
        $view = "Division/Village/Index";

        return Inertia::render($view,[
            'villages'=> $villages
        ])->table(function(InertiaTable $table) {
            $this->village->columnsDataTable($table);
        });
    }
}
