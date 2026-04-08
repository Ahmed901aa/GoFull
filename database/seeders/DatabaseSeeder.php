<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\Banner;
use App\Models\FuelPrice;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ── Admin ────────────────────────────────────────────
        User::query()->updateOrCreate(
            ['phone' => '0910406699'],
            [
                'name' => 'Admin',
                'password' => '12345678',
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        // ── Fuel Prices ──────────────────────────────────────
        FuelPrice::updateOrCreate(
            ['fuel_type' => 'petrol', 'name_ar' => 'بنزين'],
            ['price_per_liter' => 0.75, 'is_active' => true]
        );
        FuelPrice::updateOrCreate(
            ['fuel_type' => 'diesel', 'name_ar' => 'ديزل'],
            ['price_per_liter' => 0.85, 'is_active' => true]
        );
        // Deactivate old 91/95 entries if they exist
        FuelPrice::where('name_ar', 'بنزين 91')->update(['is_active' => false]);
        FuelPrice::where('name_ar', 'بنزين 95')->update(['is_active' => false]);

        // ── Banners / Offers ─────────────────────────────────
        Banner::updateOrCreate(
            ['title' => 'خصم 20% على أول طلب وقود'],
            [
                'subtitle' => 'استخدم الكود للحصول على الخصم',
                'image_url' => null,
                'discount_code' => 'GO20',
                'color_hex' => '#004B3B',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );
        Banner::updateOrCreate(
            ['title' => 'خصم 20% على أول طلب ونش'],
            [
                'subtitle' => 'استخدم الكود للحصول على الخصم',
                'image_url' => null,
                'discount_code' => 'GO20',
                'color_hex' => '#006B52',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        // ── App Settings ─────────────────────────────────────
        AppSetting::setValue('service_fee', '15.00');
        AppSetting::setValue('currency', 'د.ل');
        AppSetting::setValue('currency_code', 'LYD');
        AppSetting::setValue('towing_base_price', '50.00');
        AppSetting::setValue('app_name', 'GO FULL');
        AppSetting::setValue('support_phone', '0915909734');
    }
}
