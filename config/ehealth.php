<?php 
return [
    'header'=>[
        "Accept"         => "application/json",
        "Content-Type"   => "application/json",
        'x-access-code'  =>env('EHEALTH_ACCESS_CODE')
    ],
    'token'=>env('API_TOKEN'),
    'ehealth_api_url'=>env('EHEALTH_API_URL'),
    'ehealth_client_key'=>env('EHEALTH_CLIENT_KEY')
];
