<?php
namespace App\Repositories;

use App\Repositories\Base\BaseRepository;
use Illuminate\Contracts\Container\Container;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\LogAccessRequestApi;

class LogAccessRequestApiRepository extends BaseRepository
{

	public function __construct(Container $container)
    {
        $this->setContainer($container)
             ->setModel(\App\Models\LogAccessRequestApi::class)
			 ->setRepositoryId('rinvex.repository.uniqueid');

    }

    public function getDataTable()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $value = strtolower($value);
                $query->where("description", 'ILIKE', "%{$value}%")
                    ->orWhere("response_message", 'ILIKE', "%{$value}%");
            });
        });
        $logAccessRequestApi = QueryBuilder::for(app(LogAccessRequestApi::class)->query())
            ->defaultSort('-id')
            ->allowedFields(['id','description','request_method','request_url','request_param','response_data','response_message','causer_id','created_at'])
            ->with('causerId')
            ->allowedFilters(['description','request_method','request_url','response_message',
                            AllowedFilter::callback('date', function ($query, $value) {
                                $dates = explode("to", $value);
                                $from_date = !empty($dates['0'])?$dates['0']:'';
                                $to_date = !empty($dates['1'])?$dates['1']:'';
                                if(!empty($from_date) && empty($to_date)){
                                    $query->whereDate('created_at','=',$from_date);
                                }
                                if(!empty($from_date) && !empty($to_date)){
                                    $query->whereDate('created_at','>=',$from_date)
                                        ->whereDate('created_at','<=',$to_date);
                                }
                            }),$globalSearch])
            ->paginate()
            ->withQueryString();

        return $logAccessRequestApi;
    }

    public function columnsDataTable($table)
    {       $table->perPageOptions(config('core.per_page_option'));
            $table->selectFilter(key: 'date', label: 'Date From To', options: []);
            $table->withGlobalSearch();
            $table->column(key:'id',label:'#');
            $table->column(key:'description',label:'Description');
            $table->column(key:'request_url',label:'Request url');
            $table->column(key:'response_message' ,label:'Response message');
            $table->column(key:'request_param',label:'Request param');
            $table->column(key:'response_data',label:'Response Data');
            $table->column(key:'causerId',label:'Causer');
            $table->column(key:'created_at',label:'Created Date');
        return $table;
    }
}