<?php

namespace App\Observers;

use App\Models\Domain;

class DomainObserver
{

        /**
     * Handle the Domain "creating" event.
     *
     * @param  \App\Models\Domain  $domain
     * @return void
     */

    public function creating(Domain $domain){
        $suffix = '.' . config('tenancy.tenancy_domains')[0];
        if (! str_ends_with($domain->domain, $suffix)) {
            $domain->domain = $domain->domain . $suffix;
        }
    }

    public function updating(Domain $domain){
        $suffix = '.' . config('tenancy.tenancy_domains')[0];
        if (! str_ends_with($domain->domain, $suffix)) {
            $domain->domain = $domain->domain . $suffix;
        }
    }
}
