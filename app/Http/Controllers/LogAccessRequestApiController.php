<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use App\Repositories\LogAccessRequestApiRepository;

class LogAccessRequestApiController extends Controller
{
    protected $logAccessRequestApi;
    public function __construct()
    {
        $this->logAccessRequestApi = app(LogAccessRequestApiRepository::class);
    }

    public function index()
    {
        $logAccessRequestApi = $this->logAccessRequestApi->getDataTable();
        $view = "LogAccessRequestApi/Index";
        return Inertia::render($view, [
            'logAccessRequestApi' => $logAccessRequestApi
        ])->table(function (InertiaTable $table)  {
            $this->logAccessRequestApi->columnsDataTable($table);
        });
    }
}
