<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;
use Illuminate\Contracts\Container\Container;


class CompanyRepository extends BaseRepository
{
	public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(\App\Models\Company::class)
             ->setRepositoryId('rinvex.repository.uniqueid');

    }
}