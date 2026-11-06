<?php

namespace App\Console\Commands;

use App\Models\RebuildDomain;
use Illuminate\Console\Command;

class RebuildDomainCommand extends Command
{
    protected $signature = 'py:rebuild-domain';

    protected $description = 'Tree rebuild to table domains';

    public function handle()
    {
        // tree rebuilding to table domains 
        RebuildDomain::rebuild(true);
        $this->info('Tree rebuild to table domains done'); 

        return Command::SUCCESS;
    }
}
