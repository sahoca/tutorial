<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin kullanıcı
        User::updateOrCreate(
            ['email' => 'admin@analizdegerleme.com'],
            [
                'name' => 'Yönetici',
                'password' => Hash::make('admin1234'),
                'is_admin' => true,
            ]
        );

        // Varsayılan site ayarları
        $settings = [
            'site_name' => 'Analiz Değerleme',
            'site_tagline' => 'Gayrimenkul değerleme ve analiz hizmetleri',
            'site_description' => 'Analiz Değerleme — güvenilir gayrimenkul değerleme ve danışmanlık.',
            'contact_email' => 'info@analizdegerleme.com',
            'contact_phone' => '',
            'contact_address' => '',
            'footer_text' => '© ' . date('Y') . ' Analiz Değerleme. Tüm hakları saklıdır.',
        ];
        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Örnek sayfalar (yedek geldiğinde içerik güncellenecek)
        $pages = [
            ['title' => 'Hakkımızda', 'slug' => 'hakkimizda', 'content' => '<p>Şirket tanıtım metni buraya gelecek.</p>'],
            ['title' => 'Hizmetlerimiz', 'slug' => 'hizmetlerimiz', 'content' => '<p>Sunulan hizmetler buraya gelecek.</p>'],
        ];
        foreach ($pages as $page) {
            Page::updateOrCreate(['slug' => $page['slug']], $page + ['status' => 'published']);
        }

        // Üst menü
        $menu = [
            ['label' => 'Hakkımızda', 'url' => '/hakkimizda', 'sort_order' => 1],
            ['label' => 'Hizmetlerimiz', 'url' => '/hizmetlerimiz', 'sort_order' => 2],
        ];
        foreach ($menu as $item) {
            MenuItem::updateOrCreate(
                ['label' => $item['label'], 'location' => 'header'],
                $item + ['location' => 'header']
            );
        }
    }
}
