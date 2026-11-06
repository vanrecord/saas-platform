<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controller;
use Inertia\Inertia;

class IndexController extends Controller
{
   public function index()
   {
      $host = request()->getHost();
      $domain = \Stancl\Tenancy\Database\Models\Domain::where('domain', $host)->first();
      if ($domain) {
         tenancy()->initialize($domain->tenant);
         return app(\App\Http\Controllers\Tenant\Auth\AuthenticatedSessionController::class)->create();
      }

      $config_api_url = config("frontend-sites.app_frontend_api_url");
      $config_img_url = config("frontend-sites.app_frontend_image_url");

      try {
         $home_page = Http::timeout(3)->get($config_api_url.'/home');
         $home_page_data = $home_page->successful() ? json_decode($home_page->json()) : null;
      } catch (\Throwable $e) {
         $home_page_data = null;
      }

      try {
         $users = Http::timeout(3)->get($config_api_url.'/users');
         $users_data = $users->successful() ? $users->json() : [];
      } catch (\Throwable $e) {
         $users_data = [];
      }

      $default_home_page = (object)[
         'homepage_title' => 'Dental Clinic',
         'homepage_description' => 'With our modern dental clinic, we provide professional and comprehensive dental care for you and your family.',
         'homepage_logo' => '',
         'homepage_copyright' => '© ' . date('Y') . ' Dental Clinic. All rights reserved.'
      ];

      return Inertia::render('Frontend/Index',
            [
               'config_api_url'=> $config_api_url,
               'image_url'    => $config_img_url,
               'home_page'    => fn () => $home_page_data ?: $default_home_page,
               'users'        => fn () => $users_data
            ]);
   }
}