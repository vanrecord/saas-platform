<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Supports\Trait\LogsApi;
use Illuminate\Support\Facades\Schema;

class LogRouteAPI
{
    use LogsApi;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $response_message = json_decode($response->getContent(), true);
        $data = [
            'request_url' => $request->url(),
            'request_method' => $request->getMethod(),
            'request_param' => $request->all(),
            'response_data' => json_validate($response->getContent())?$response->getContent():json_encode($response->getContent()),
            'response_message' => !empty($response_message['message'])?$response_message['message']:null,
        ];
        if(Schema::hasTable('log_access_request_apis')) {
            $this->saveLogAccessApi($data);
        }

        return $response;
    }
}
