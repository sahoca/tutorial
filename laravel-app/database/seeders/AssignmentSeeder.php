<?php

namespace Database\Seeders;

use App\Models\Assignment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AssignmentSeeder extends Seeder
{
    public function run(): void
    {
        // SPK Surekli Bilgilendirme kapsaminda yayinlanan gercek belgeler
        // (analizdegerleme.com sitesinden alindi).
        $base = '/storage/uploads/2026/01/';

        $docs = [
            ['2024 Yılı Bağımsız Denetim Raporu', 'SPK', '2024', '2024-12-31', $base . 'Analiz-Degerleme-Bagimsiz-Denetim-Raporu-2024.pdf'],
            ['2023 Yılı Bağımsız Denetim Raporu', 'SPK', '2023', '2023-12-31', $base . 'Analiz-Degerleme-Bagimsiz-Denetim-Raporu-2023.pdf'],
            ['2022 Yılı Bağımsız Denetim Raporu', 'SPK', '2022', '2022-12-31', $base . 'Analiz-Degerleme-Bagimsiz-Denetim-Raporu-2022.pdf'],
            ['Kuruluş Ticari Sicil Gazetesi', 'SPK', '10604', '2022-06-22', $base . '2022-10604.pdf'],
            ['Ticari Sicil Gazetesi', 'SPK', '10640', '2022-08-16', $base . '2022-10640.pdf'],
            ['Ticari Sicil Gazetesi', 'SPK', '10645', '2022-08-23', $base . '2022-10645.pdf'],
            ['Ticari Sicil Gazetesi', 'SPK', '10648', '2022-08-26', $base . '2022-10648.pdf'],
            ['Ticari Sicil Gazetesi', 'SPK', '10649', '2022-08-29', $base . '2022-10649.pdf'],
            ['Ticari Sicil Gazetesi', 'SPK', '10988', '2023-12-27', $base . '2023-10988.pdf'],
            ['Ticari Sicil Gazetesi', 'SPK', '11240', '2024-12-31', $base . '2024-11240.pdf'],
            ['Ticari Sicil Gazetesi', 'SPK', '11369', '2025-07-10', $base . '2025-11369.pdf'],
            ['Ticari Sicil Gazetesi', 'SPK', '11480', '2025-12-16', $base . '2025-11480.pdf'],
        ];

        $order = 0;
        foreach ($docs as [$title, $authority, $ref, $date, $url]) {
            $fullTitle = $ref && ! Str::contains($title, $ref) ? "{$title} ({$ref})" : $title;
            Assignment::updateOrCreate(
                ['slug' => Str::slug($fullTitle . '-' . $ref)],
                [
                    'title' => $fullTitle,
                    'authority' => $authority,
                    'reference_no' => $ref,
                    'assignment_date' => $date,
                    'description' => 'Sermaye Piyasası Kurulu sürekli bilgilendirme yükümlülüğü kapsamında yayımlanan belge.',
                    'document_url' => $url,
                    'status' => 'completed',
                    'is_published' => true,
                    'sort_order' => $order++,
                ]
            );
        }

        // Ornek SPK/BDDK gorevlendirme kayitlari (yapi gostermek icin)
        Assignment::updateOrCreate(
            ['slug' => 'spk-gayrimenkul-degerleme-yetkisi'],
            [
                'title' => 'SPK Gayrimenkul Değerleme Yetkilendirmesi',
                'authority' => 'SPK',
                'reference_no' => 'III-62.3',
                'assignment_date' => '2022-09-01',
                'description' => "Şirketimiz, Sermaye Piyasası Kurulu'nun III-62.3 sayılı \"Sermaye Piyasasında Faaliyette Bulunacak Gayrimenkul Değerleme Kuruluşları Hakkında Tebliğ\" çerçevesinde gayrimenkul değerleme hizmeti vermek üzere yetkilendirilmiştir.",
                'status' => 'active',
                'is_published' => true,
                'sort_order' => 50,
            ]
        );

        Assignment::updateOrCreate(
            ['slug' => 'bddk-degerleme-yetkilendirmesi'],
            [
                'title' => 'BDDK Bankalara Değerleme Hizmeti Yetkilendirmesi',
                'authority' => 'BDDK',
                'reference_no' => '—',
                'assignment_date' => '2022-09-01',
                'description' => 'Şirketimiz, Bankacılık Düzenleme ve Denetleme Kurumu düzenlemeleri kapsamında bankalara gayrimenkul değerleme hizmeti vermek üzere yetkili kuruluşlar listesinde yer almaktadır.',
                'status' => 'active',
                'is_published' => true,
                'sort_order' => 51,
            ]
        );
    }
}
