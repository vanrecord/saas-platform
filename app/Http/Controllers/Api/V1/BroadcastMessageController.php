<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\Carbon;
use App\Models\Tenant;
use App\Enums\ApiStatusCode;
use Illuminate\Http\Request;
use App\Models\EmployeeTenant;
use App\Models\BroadcastMessage;
use App\Http\Controllers\Controller;
use App\Supports\Trait\ApiResponseTrait;
use Illuminate\Support\Facades\Validator;
use App\Repositories\BroadcastMessageRepository;
use App\Repositories\DomainRepository;

class BroadcastMessageController extends Controller
{

    use ApiResponseTrait;

    protected $domain;
    protected $broadcastMessage;
    
    public function __construct()
    {
        $this->domain = App(DomainRepository::class);
        $this->broadcastMessage = App(BroadcastMessageRepository::class);
    }

    /**
     * 1. List Event
     *
     * This endpoint retrieves a list of event with optional search functionality.
     * @group Event
     * @authenticated
     * @header Authorization Bearer "YOUR_ACTUAL_TOKEN"
     * 
     * You can call message translate en or km (Accept-Language : km) 
     * if you not add Accept-Language on heander it call defaulth en
     * @header Accept-Language en,km defauth en
     * 
     * @response status=100 {
     *     "status": 100,
     *     "message": "Successfully",
     *     "datas": {
     *         "current_page": 1,
     *         "data": [
     *             {
     *                   "id": 1,
     *                   "event_id": "4"
     *                   "title": "Title Event",
     *                   "description": "Description event...",
     *                   "image": null,
     *                   "allow_accept": false,
     *                   "date": "Event on 06 June 2025",
     *                   "domain": "https://testing.pethyoeungbms.com"
     *                   "ehealth_code": "f3e460f8-9be4-4018-8b1e-4ff8f16163e1"
     *                   ...
     *             }
     *         ],
     *         ...
     *     }
     * }
     * @response status=400 {"status": 400,"message": "Bad request"}
     * 
     * @bodyParam ehealth_code int required The eHealth code of the driver. Example: 8553290000021212 
     * @queryParam page int The page number for pagination. Example: 1
     * @queryParam perPage int The number of results per page. Example: 15
     * @queryParam search string The search term to filter by title event. Example: Meeting monthly
     * @queryParam word_limit int The number of limit word description is default 10 word. Example: 15
     */

    public function listEvents(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ehealth_code'  => ['required', 'max:20'],
        ]);
        if($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }
        // Get the search term from the request & word limit
        $search = $request->input('search');
        $word_limit = $request->input('word_limit');

        $broadcast_message = $this->broadcastMessage->broadcastEvent()
                                ->when($search, function ($query) use ($search) {
                                    $query->where('title', 'ILIKE', "%$search%");
                                })
                                ->whereDate('start_date','>=',Carbon::now()->format('Y-m-d'))
                                ->where('status', '!=',BroadcastMessage::DRAFT)
                                ->orderBy('start_date', 'desc')
                                ->paginate();
        $data_event = [];
        $event = $broadcast_message
                ->map(function ($event) use ($request,$word_limit,&$data_event) {
                    $domain = $this->domain->query()->where('tenant_id',$event->tenant_id)->first();
                    $datas = [
                        'id'           => $event->id,
                        'event_id'     => $event->broadcast_message_tenant_id,
                        'title'        => $event->title,
                        'description'  => truncate_string_by_words(preg_replace('/\s+/', ' ',html_entity_decode(strip_tags($event->message))), $word_limit??10),
                        'image'        => !empty($event->image_event_broadcast_url)?$event->image_event_broadcast_url:null,
                        'allow_accept' => $event->allow_accept,
                        'date'         => 'Event on '.Carbon::createFromFormat('d/m/Y H:i', $event->start_date)->format('d F Y'),
                        'domain'                    => 'https://' . $domain->domain,
                        'ehealth_organization_code' => $domain->ehealth_organization_code,
                        'is_required_accept' => $event->is_required_accept,
                        'expire_accept_date' => $event->expire_accept_date,
                        'start_date'         => $event->start_date,
                    ];
                    if($event->visibility == BroadcastMessage::PUBLIC){
                        return $data_event[] = $datas;
                    }else {
                        $tenant = Tenant::find($event->tenant_id);
                        
                        $tenant->run(function ($tenant) use ($event,$request, $datas ,&$data_event)  {
                            $employee = App(EmployeeTenant::class)->query()->where('ehealth_code', $request->ehealth_code)->first();
                            if($employee){
                                $user_ids = array_filter($event->event, function ($item) use ($employee) {
                                                return isset($item['user_ids']) && in_array($employee->id, $item['user_ids']);
                                            });
                                if(count($user_ids) > 0){
                                    return $data_event[] = $datas;
                                }
                            }
                        });
                    }
                    
                });
        $datas = [
            'data' =>  $data_event,
            'current_page'  => $broadcast_message->currentPage(),
            'per_page'      => $broadcast_message->perPage(),
            'last_page'     => $broadcast_message->lastPage(),
            'next_page_url' => $broadcast_message->nextPageUrl(),
            'prev_page_url' => $broadcast_message->previousPageUrl(),
            'total'         => count($data_event),
        ];
        return $this->successResponse($datas, null, ApiStatusCode::SUCCESS);

    }
}
