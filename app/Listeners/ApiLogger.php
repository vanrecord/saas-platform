<?php

namespace App\Listeners;

use App\Supports\Trait\LogsApi;
use Illuminate\Http\Client\Events\ResponseReceived;

class ApiLogger
{
    use LogsApi;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Http\Client\Events\ResponseReceived $event
     * @return void
     */
    public function handle(ResponseReceived $event)
    {
        $data_response = $event->response;
        $body = $data_response->getBody()->getContents();
        $data_decode = json_decode($body, true);
        $data_log = [
            'request_method'   => $event->request->method(),
            'request_url'      => $event->request->url(),
            'request_param'    => !empty(aes_decryption($event->request->body()))?aes_decryption(json_decode($event->request->body(),true)):aes_decryption($event->request->body()),
            'response_data'    => json_validate($body)?$body:json_encode($body),
            'response_message' => !empty($data_decode['message'])?$data_decode['message']:null
        ];
        $this->saveLogRequestApi($data_log);
        return $body;
    }
}
