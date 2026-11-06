<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Repositories\ProvinceRepository;
use App\Models\Province;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class ProvinceController extends Controller
{
    public function __construct()
    {
        $this->province = App(ProvinceRepository::class);
    }

    public function index()
    {
        $this->authorize(app("request")->route()->getName(), Province::class);
        $provinces = $this->province->getDataTable();
        $view = "Division/Province/Index";

        return Inertia::render($view,[
            'provinces'=> $provinces
        ])->table(function(InertiaTable $table) {
            $this->province->columnsDataTable($table);
        });
    }
}
