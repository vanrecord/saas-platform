<?php
namespace App\Supports\Trait;

use App\Models\User;
use App\Models\LogAccessRequestApi;
use App\Repositories\LogAccessRequestApiRepository;

trait LogsApi
{
    public function saveLogAccessApi($datas=[])
    {
        try {
            $causer_id = auth()->user()?->id;
            $data_log = [
                'description'      => LogAccessRequestApi::ACTION_ACCESS,
                'causer_id'        => $causer_id,
                'causer_type'      => User::class,
                'request_method'   => $datas['request_method']?$datas['request_method']:null,
                'request_url'      => $datas['request_url']?$datas['request_url']:null,
                'request_param'    => $datas['request_param']?json_encode($datas['request_param']):null,
                'response_data'    => $datas['response_data']?$datas['response_data']:null,
                'response_message' => $datas['response_message']?json_encode($datas['response_message']):null
            ];
            App(LogAccessRequestApiRepository::class)->create($data_log);
        } catch (\Throwable $e) {
            // Log access API failure should not interrupt the main request
        }
    }

    public function saveLogRequestApi($datas=[])
    {
        try {
            $causer_id = auth()->user()?->id;
            $data_log = [
                'description'      => LogAccessRequestApi::ACTION_REQUEST,
                'causer_id'        => $causer_id,
                'causer_type'      => User::class,
                'request_method'   => !empty($datas['request_method'])?$datas['request_method']:null,
                'request_url'      => !empty($datas['request_url'])?$datas['request_url']:null,
                'request_param'    => !empty($datas['request_param'])?$datas['request_param']:null,
                'response_data'    => !empty($datas['response_data'])?$datas['response_data']:null,
                'response_message' => !empty($datas['response_message'])?json_encode($datas['response_message']):null
            ];
            App(LogAccessRequestApiRepository::class)->create($data_log);
        } catch (\Throwable $e) {
            // Log request API failure should not interrupt the main request
        }
    }
}