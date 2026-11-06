<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stancl\Tenancy\Concerns\HasATenantsOption;
use Stancl\Tenancy\Concerns\ExtendsLaravelCommand;
use App\Supports\Trait\DivisionTrait;

class SeedDivisionTenant extends Command
{
    use HasATenantsOption,ExtendsLaravelCommand, DivisionTrait;
    protected $description = 'Seed division to tenant';

    
    protected static function getTenantCommandName(): string
    {
        return 'py:seed-division-tenant';
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        tenancy()->runForMultiple($this->option('tenants'), function ($tenant) {
            $this->line("Tenant: {$tenant->getTenantKey()}");
            $this->Provinces();
            $this->Districts();
            $this->Communes();
            $this->Villages();
        });
    }
}
