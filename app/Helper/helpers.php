<?php 

use App\Models\Country;
use App\Models\State;

if(!function_exists('assetStorage')){
    function assetStorage($string){
        if(isset(tenancy()->tenant->id)){
            return tenant_asset('').preg_replace('/.+storage/i', '', $string);
        }else{
            return $string;
        }
    }
}

if(!function_exists('getCountryData')){
    function getCountryData($id = null){
        $data = tenancy()->central(function($tenancy) use($id){
            if(!empty($id)){
                return Country::find($id);
            }else{
                return Country::get();
            }
        });
        return $data;
    }
}

if(!function_exists('getStateData')){
    function getStateData($id = null){
        $data = tenancy()->central(function() use($id){
            if(!empty($id)){
                return State::find($id);
            }else{
                return State::get();
            }
        });
        return $data;
    }
}

if (!function_exists('aes_encryption')) { 
    function aes_encryption($data){ 
        $client_key     = config('ehealth.ehealth_client_key');
        $key            = $client_key?substr($client_key, 0, 32):'9p3cfVSYFkmWZNP9FxIQbF3DZ9UynVtj'; // 32 chars
        $iv             = $client_key?substr($client_key, 0, 16):'TBIq3wDECc5f9KWs'; // 16 chars
        $method         = 'aes-256-ctr'; 
        return openssl_encrypt($data,$method,$key,0,$iv);
    } 
}

if (!function_exists('aes_decryption')) { 
    function aes_decryption($data){

        $client_key     = config('ehealth.ehealth_client_key');
        $key            = $client_key?substr($client_key, 0, 32):'9p3cfVSYFkmWZNP9FxIQbF3DZ9UynVtj'; // 32 chars
        $iv             = $client_key?substr($client_key, 0, 16):'TBIq3wDECc5f9KWs'; // 16 chars
        $method         = 'aes-256-ctr'; 
        return is_array($data)?json_encode($data):openssl_decrypt($data,$method,$key,0,$iv);
    } 
}

if (!function_exists('round_up')) { 
    function round_up ( $value, $precision ) { 
        $pow = pow ( 10, $precision );
        return ( ceil ( round($pow * $value) ) + ceil ( $pow * $value - ceil ( $pow * $value ) ) ) / $pow; 
    } 
}

if (!function_exists('round_down')) { 
    function round_down ( $value, $precision ) { 
        $mult = pow ( 10, $precision ); 
        return floor($value * $mult) / $mult;
    } 
}

if (!function_exists('endsWith')) { 
    function endsWith($string, $endString)
    {
        $len = strlen($endString);
        if ($len == 0) {
            return true;
        }
        return (substr($string, -$len) === $endString);
    }
}

if (!function_exists('encryptionQrInfo')) {
    /**
     * Generate the QR info URL with the encrypted parameters.
     *
     * @param int $id
     * @param string $type
     * @return string
     */
    function encryptionQrInfo($id, $type)
    {
        $organizationCode = optional(tenant()->domains[0])->ehealth_organization_code;
        $qr_string = 'qr=' . $type . $id . '@org' . $organizationCode;
        $encrypted = aes_encryption($qr_string, 'bms');
        return $encrypted;
    }
}

if (!function_exists('truncate_string_by_words')) { 
    function truncate_string_by_words(string $string, int $word_limit = 10): string
    {
        // Split the string into an array of words
        $words = explode(' ', $string);
        // Check if the number of words is greater than the limit
        if (count($words) > $word_limit) {
            // array_slice extracts a portion of the array.
            // The '0' explicitly tells it to start from the very first word (index 0).
            $sliced_words = array_slice($words, 0, $word_limit);
            // Join the extracted words and add "..."
            return implode(' ', $sliced_words) . '...';
        }

        // If the string is not longer than the limit, return it unchanged
        return $string;
    }
}

