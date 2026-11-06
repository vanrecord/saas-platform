<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Repositories\DistrictRepository;
use App\Models\District;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class DistrictController extends Controller
{
    public function __construct()
    {
        $this->district = App(DistrictRepository::class);
    }

    public function index()
    {
        $this->authorize(app("request")->route()->getName(), District::class);
        $districts = $this->district->getDataTable();
        $view = "Division/District/Index";

        return Inertia::render($view,[
            'districts'=> $districts
        ])->table(function(InertiaTable $table) {
            $this->district->columnsDataTable($table);
        });
    }
}
