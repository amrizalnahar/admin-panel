<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'site_name' => 'Admin Panel',
            'site_description' => 'Aplikasi manajemen konten berbasis Laravel.',

            'contact_email' => null,
            'contact_phone' => null,
            'contact_address' => null,
            'mail_from_address' => 'admin@example.com',
            'social_facebook' => 'https://facebook.com/',
            'social_instagram' => 'https://instagram.com/',
            'social_whatsapp' => null,
            'social_tiktok' => null,
            'social_twitter' => null,
            'social_youtube' => null,
            'seo_site_name' => 'Admin Panel',
            'seo_description' => 'Aplikasi manajemen konten berbasis Laravel.',
            'seo_author' => 'Admin Panel',
            'ga4_measurement_id' => null,
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::setValue($key, $value);
        }
    }
}
