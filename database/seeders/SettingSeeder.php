<?php

namespace Database\Seeders;

use App\Enums\UnitType;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'site_name', 'value' => 'Universitas Bumigora', 'type' => 'text'],
            ['key' => 'site_description', 'value' => 'Universitas Bumigora - Kampus Unggulan di Nusa Tenggara Barat', 'type' => 'textarea'],
            ['key' => 'site_keywords', 'value' => 'universitas bumigora, ubg, kampus ntb, perguruan tinggi lombok', 'type' => 'text'],

            // Contact
            ['key' => 'address', 'value' => 'Jl. Ismail Marzuki No. 22, Cilinaya, Kec. Cakranegara, Kota Mataram, Nusa Tenggara Barat 83239', 'type' => 'textarea'],
            ['key' => 'phone', 'value' => '(0370) 633837', 'type' => 'text'],
            ['key' => 'email', 'value' => 'info@ubg.ac.id', 'type' => 'email'],
            ['key' => 'whatsapp', 'value' => '62817788899', 'type' => 'text'],

            // Social Media
            ['key' => 'facebook', 'value' => 'https://facebook.com/universitasbumigora', 'type' => 'url'],
            ['key' => 'instagram', 'value' => 'https://instagram.com/universitasbumigora', 'type' => 'url'],
            ['key' => 'youtube', 'value' => 'https://youtube.com/@universitasbumigora', 'type' => 'url'],
            ['key' => 'twitter', 'value' => '', 'type' => 'url'],
            ['key' => 'linkedin', 'value' => '', 'type' => 'url'],
            ['key' => 'tiktok', 'value' => '', 'type' => 'url'],

            // Theme
            ['key' => 'theme_color_primary', 'value' => '#1e40af', 'type' => 'color'],
            ['key' => 'theme_color_secondary', 'value' => '#64748b', 'type' => 'color'],
            ['key' => 'theme_color_accent', 'value' => '#f59e0b', 'type' => 'color'],
            ['key' => 'theme_font_heading', 'value' => 'Inter', 'type' => 'text'],
            ['key' => 'theme_font_body', 'value' => 'Inter', 'type' => 'text'],

            // Features
            ['key' => 'show_announcement_bar', 'value' => 'true', 'type' => 'boolean'],
            ['key' => 'show_floating_whatsapp', 'value' => 'true', 'type' => 'boolean'],
            ['key' => 'show_back_to_top', 'value' => 'true', 'type' => 'boolean'],
            ['key' => 'enable_dark_mode', 'value' => 'false', 'type' => 'boolean'],

            // Content
            ['key' => 'articles_per_page', 'value' => '12', 'type' => 'integer'],
            ['key' => 'featured_articles_count', 'value' => '4', 'type' => 'integer'],
            ['key' => 'sidebar_articles_count', 'value' => '5', 'type' => 'integer'],

            // Footer
            ['key' => 'footer_text_left', 'value' => '© 2026 Universitas Bumigora. All rights reserved.', 'type' => 'text'],
            ['key' => 'footer_text_right', 'value' => 'Developed with ❤️ by PUSTIK UBG', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                [
                    'unit_type' => UnitType::UNIVERSITAS,
                    'unit_id' => null,
                    'key' => $setting['key'],
                ],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                ]
            );
        }

        $this->command->info('Settings seeded successfully!');
        $this->command->info('Total Settings: ' . Setting::count());
    }
}
