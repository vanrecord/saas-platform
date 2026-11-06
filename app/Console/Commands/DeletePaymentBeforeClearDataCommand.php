<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Stancl\Tenancy\Concerns\HasATenantsOption;

class DeletePaymentBeforeClearDataCommand extends Command
{
    use HasATenantsOption;

    protected $description = 'Delete payment that their order was cleared';

    public function __construct()
    {
        parent::__construct();
        $this->setName('fwta:delete-payment-before-order');
    }

    public function handle()
    {
        tenancy()->runForMultiple($this->option('tenants'), function ($tenant) {
            
            $this->line("Tenant: {$tenant->getTenantKey()}");

            \DB::statement('delete from payments where created_at < (select min(created_at) from orders)');
            
        });

        $this->line('');

        return Command::SUCCESS;
    }
}
