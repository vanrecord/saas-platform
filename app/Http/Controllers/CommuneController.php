<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Repositories\CommuneRepository;
use App\Models\Commune;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class CommuneController extends Controller
{
    public function __construct()
    {
        $this->commune = App(CommuneRepository::class);
    }

    public function index()
    {
        $this->authorize(app("request")->route()->getName(), Commune::class);
        $communes = $this->commune->getDataTable();
        $view = "Division/Commune/Index";

        return Inertia::render($view,[
            'communes'=> $communes
        ])->table(function(InertiaTable $table) {
            $this->commune->columnsDataTable($table);
        });
    }
}
