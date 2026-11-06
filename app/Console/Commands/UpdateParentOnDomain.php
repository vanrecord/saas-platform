<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use App\Models\Domain;
use App\Models\Tenant;
use Illuminate\Console\Command;
use App\Repositories\DomainRepository;

class UpdateParentOnDomain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'py:update-parent-on-domain {--domain_id=} {--parent_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update parent id on domain. --domain_id[=DOMAIN_ID] --parent_id[=PARENT_ID]';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        if(!$this->option('domain_id')){
            $this->error('domain_id is required.');
            return ;
        }
        if(!$this->option('parent_id')){
            $this->error('parent_id is required.');
            return ;
        }

        $domain = Domain::find($this->option('domain_id'));
        $domain->update(['domain'=>$domain->tenant->domain,'parent_id' => $this->option('parent_id')]);
        $domain->tenant->update(['parent_domain_id' => (int) $this->option('parent_id')]);
        $this->info('Update parent id on domain done.');
        return Command::SUCCESS;
    }
}
