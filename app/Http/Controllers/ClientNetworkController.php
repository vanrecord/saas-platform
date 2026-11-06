<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controller;
use Inertia\Inertia;

class ClientNetworkController extends Controller
{
   public  function provider($id)
   {
      $config_api_url = config("frontend-sites.app_frontend_api_url");
      $config_img_url = config("frontend-sites.app_frontend_image_url");
      $home_page      = Http::get($config_api_url.'/home');
      $client_networks = Http::get($config_api_url."/client-network-provider/{$id}");
      foreach($client_networks->json() as $client_network){
         $client_network_provider = $client_network;
      }
      return Inertia::render('Frontend/ClientNetwork',
            [
               'config_api_url'=> $config_api_url,
               'image_url'    => $config_img_url,
               'home_page'    => fn () => json_decode($home_page->json()),
               'client_network_provider' => $client_network_provider,
            ]);
   }
}