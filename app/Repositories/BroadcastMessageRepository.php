<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;
use Illuminate\Contracts\Container\Container;
use App\Models\BroadcastMessage;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;
use Carbon\Carbon;
use App\Models\Category;

class BroadcastMessageRepository extends BaseRepository
{
	public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(BroadcastMessage::class)
             ->setRepositoryId('rinvex.repository.uniqueid');
    }

}