<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

/**
 * Photo banners for the app home page (Libyan fuel stations).
 * Images live in storage/app/public/banners — run `php artisan storage:link`
 * once so they're publicly served.
 *
 * Run: php artisan db:seed --class=StationBannersSeeder
 */
class StationBannersSeeder extends Seeder
{
    public function run(): void
    {
        $stations = [
            [
                'title' => 'محطة الراحلة',
                'subtitle' => 'شريك التزوّد بالوقود في ليبيا',
                'image_url' => 'banners/rahila_station.jpg',
                'sort_order' => 100,
            ],
            [
                'title' => 'محطة الشرارة',
                'subtitle' => 'شركة الشرارة الذهبية للخدمات النفطية',
                'image_url' => 'banners/sharara_station.jpg',
                'sort_order' => 101,
            ],
            [
                'title' => 'عروض وخصومات GoFull',
                'subtitle' => 'خصم على خدمة الساحبة — اطلب الآن',
                'image_url' => 'banners/gofull_towing_offer.png',
                'sort_order' => 99,
            ],
        ];

        foreach ($stations as $station) {
            Banner::updateOrCreate(
                ['title' => $station['title']],
                $station + ['is_active' => true]
            );
        }
    }
}
