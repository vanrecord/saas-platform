<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controller;
use Inertia\Inertia;

class FAQController extends Controller
{
   public  function index()
   {
      $config_api_url = config("frontend-sites.app_frontend_api_url");
      $config_img_url = config("frontend-sites.app_frontend_image_url");
      return Inertia::render('Frontend/Faq',[
         'config_api_url'=> $config_api_url,
         'image_url'     => $config_img_url,
      ]);
   }
}