<?php

namespace App\Supports\Trait;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

trait ClinicInfoTrait
{
    /**
     * Retrieve clinic info from settings, company, hospital, and tenant sources.
     *
     * @return array
     */
    public function getClinicInfo(): array
    {
        $settings = [];
        try {
            if (Schema::hasTable('settings')) {
                $settings = DB::table('settings')->pluck('setting_value', 'setting_key')->toArray();
            }
        } catch (\Throwable $e) {}

        $company = null;
        try {
            if (class_exists(\App\Models\Company::class) && Schema::hasTable('companies')) {
                $company = \App\Models\Company::first();
            }
        } catch (\Throwable $e) {}

        $hospital = null;
        try {
            if (Schema::hasTable('hospitals')) {
                $hospital = DB::table('hospitals')->first();
            }
        } catch (\Throwable $e) {}

        // Custom Header Image: check settings, company media, or tenant attributes
        $headerImage = $settings['header_image'] 
            ?? $settings['invoice_header_image'] 
            ?? $settings['custom_header'] 
            ?? (function_exists('tenant') && tenant() ? (tenant('header_image') ?? tenant('invoice_header')) : null);

        if (!$headerImage && $company && method_exists($company, 'getFirstMediaUrl')) {
            $headerImage = $company->getFirstMediaUrl('header_image') ?: $company->getFirstMediaUrl('header') ?: null;
        }

        if ($headerImage && !str_starts_with($headerImage, 'http') && !str_starts_with($headerImage, '/')) {
            $headerImage = Storage::url($headerImage);
        }

        // Clinic Logo
        $logo = $settings['logo']
            ?? (function_exists('tenant') && tenant() ? tenant('logo') : null);

        if (!$logo && $company && method_exists($company, 'getFirstMediaUrl')) {
            $logo = $company->getFirstMediaUrl('logo') ?: null;
        }

        if ($logo && !str_starts_with($logo, 'http') && !str_starts_with($logo, '/')) {
            $logo = Storage::url($logo);
        }
        if (!$logo) {
            $logo = '/images/dental/logo.png';
        }

        // Clinic Names & Slogan
        $nameKh = $settings['clinic_name_kh'] 
            ?? $settings['shop_name']
            ?? $company?->name_native 
            ?? (function_exists('tenant') && tenant() ? (tenant('name_kh') ?? tenant('name_native')) : null)
            ?? 'គ្លីនិកធ្មេញ ឌែនធីសយើង';

        $nameEn = $settings['clinic_name_en'] 
            ?? $settings['name_en']
            ?? $company?->name_international 
            ?? $hospital?->name 
            ?? (function_exists('tenant') && tenant() ? (tenant('company') ?? tenant('name')) : null)
            ?? 'Dentist Yoeung Clinic';

        $slogan = $settings['clinic_slogan'] 
            ?? $settings['header_noted'] 
            ?? (function_exists('tenant') && tenant() ? tenant('slogan') : null)
            ?? 'គុណភាព សុវត្ថិភាព និងអនាម័យជាចម្បង';

        // Clinic Address & Phone
        $address = $settings['address'] 
            ?? $hospital?->address 
            ?? (function_exists('tenant') && tenant() ? tenant('address') : null)
            ?? 'Sangkat Tuek Laok I, Khan Toulkork, Phnom Penh.';

        $phone = $settings['phone'] 
            ?? $company?->phone 
            ?? $hospital?->phone 
            ?? (function_exists('tenant') && tenant() ? tenant('phone') : null)
            ?? '086761216 (Call & Telegram)';

        $noteKh = $settings['footer_noted_kh'] 
            ?? $settings['footer_noted'] 
            ?? 'បញ្ជាក់៖ ប្រាក់ដែលបានបង់រួចមិនអាចដកវិញបានទេ';

        $noteEn = $settings['footer_noted_en'] 
            ?? 'Note: All payment are not returnable';

        return [
            'header_image' => $headerImage,
            'logo' => $logo,
            'name_kh' => $nameKh,
            'name_en' => $nameEn,
            'slogan' => $slogan,
            'address' => $address,
            'phone' => $phone,
            'note_kh' => $noteKh,
            'note_en' => $noteEn,
        ];
    }
}
