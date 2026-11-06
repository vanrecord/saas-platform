<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Supports\Trait\DivisionTrait;

class SeedDivision extends Command
{
    use DivisionTrait;
    protected $signature = 'py:seed-division-central';

    protected $description = 'Seed division to central';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->Provinces();
        $this->Districts();
        $this->Communes();
        $this->Villages();
    }
}
