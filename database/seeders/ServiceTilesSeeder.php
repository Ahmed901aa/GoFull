<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

/**
 * The 3 service tiles shown on the app home grid (بنزين، ساحبة، طوارئ).
 * Images are served from storage/app/public/services — run
 * `php artisan storage:link` once.
 *
 * Run: php artisan db:seed --class=ServiceTilesSeeder
 */
class ServiceTilesSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'title' => 'بنزين',
                'type' => 'service',
                'action' => 'fuel',
                'image_url' => 'services/fuel.svg',
                'sort_order' => 1,
            ],
            [
                'title' => 'الساحبة',
                'type' => 'service',
                'action' => 'towing',
                'image_url' => 'services/towing.svg',
                'sort_order' => 2,
            ],
            [
                'title' => 'الطوارئ',
                'type' => 'service',
                'action' => 'emergency',
                'image_url' => 'services/emergency.png',
                'sort_order' => 3,
            ],
        ];

        foreach ($services as $service) {
            Banner::updateOrCreate(
                ['title' => $service['title'], 'type' => 'service'],
                $service + ['is_active' => true]
            );
        }
    }
}
