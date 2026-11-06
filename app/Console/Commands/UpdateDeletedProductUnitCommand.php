<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Stancl\Tenancy\Concerns\HasATenantsOption;

class UpdateDeletedProductUnitCommand extends Command
{
    use HasATenantsOption;

    protected $description = 'Set deleted at to product unit if the product was deleted';

    public function __construct()
    {
        parent::__construct();
        $this->setName('fwta:deleted-product-unit');
    }

    public function handle()
    {
        tenancy()->runForMultiple($this->option('tenants'), function ($tenant) {
            
            $this->line("Tenant: {$tenant->getTenantKey()}");

            Product::onlyTrashed()->with('productUnit')->get()->each(function ($item){
                return $item->productUnit()->delete();
            });
            
        });

        $this->line('');

        return Command::SUCCESS;
    }
}
