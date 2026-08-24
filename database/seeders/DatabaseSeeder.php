<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\Banner;
use App\Models\FuelPrice;
use App\Models\ProviderProfile;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Production only gets idempotent reference data — never accounts
        // with known passwords, never test orders. Create the real admin
        // via `php artisan tinker` or a dedicated command.
        if (app()->isProduction()) {
            $this->seedReferenceData();
            return;
        }

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

        // ── Test Provider (Driver App) — Ibrahim Saleh ──────
        $ibrahim = User::query()->updateOrCreate(
            ['phone' => '0923663333'],
            [
                'name' => 'ابراهيم صالح',
                'password' => '12345678',
                'role' => 'provider',
                'status' => 'active',
            ]
        );
        ProviderProfile::query()->updateOrCreate(
            ['user_id' => $ibrahim->id],
            [
                'service_type' => 'fuel_delivery',
                'vehicle_make' => 'تويوتا',
                'vehicle_model' => 'هايلكس',
                'vehicle_year' => 2022,
                'vehicle_plate' => 'أ ب م 1234',
                'vehicle_color' => 'أبيض',
                'is_available' => true,
                'verification_status' => 'approved',
                'verified_at' => now(),
                'average_rating' => 0,
                'total_ratings' => 0,
            ]
        );

        // ── Test Provider (Driver App) — Muftah Saleh (Towing) ─
        $muftah = User::query()->updateOrCreate(
            ['phone' => '0923664444'],
            [
                'name' => 'مفتاح صالح',
                'password' => '12345678',
                'role' => 'provider',
                'status' => 'active',
            ]
        );
        ProviderProfile::query()->updateOrCreate(
            ['user_id' => $muftah->id],
            [
                'service_type' => 'towing',
                'vehicle_make' => 'ميتسوبيشي',
                'vehicle_model' => 'كانتر',
                'vehicle_year' => 2020,
                'vehicle_plate' => 'ج د ه 5678',
                'vehicle_color' => 'أزرق',
                'is_available' => true,
                'verification_status' => 'approved',
                'verified_at' => now(),
                'average_rating' => 0,
                'total_ratings' => 0,
            ]
        );

        // ── Test Customer (Customer App) ─────────────────────
        User::query()->updateOrCreate(
            ['phone' => '0911111111'],
            [
                'name' => 'عميل تجريبي',
                'password' => '12345678',
                'role' => 'driver',
                'status' => 'active',
            ]
        );

        $this->seedReferenceData();

        // ── Banners / Offers ─────────────────────────────────
        Banner::updateOrCreate(
            ['title' => 'خصم 20% على أول طلب وقود'],
            [
                'subtitle' => 'استخدم الكود للحصول على الخصم',
                'image_url' => '/images/logo.png',
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
                'image_url' => '/images/logo.png',
                'discount_code' => 'GO20',
                'color_hex' => '#006B52',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        // Seed test orders & ratings for providers
        $this->call(TestOrdersAndRatingsSeeder::class);
    }

    /**
     * Idempotent reference data, safe for every environment:
     * fuel prices + app settings. No accounts, no test orders.
     */
    private function seedReferenceData(): void
    {
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

        // ── App Settings ─────────────────────────────────────
        AppSetting::setValue('service_fee', '15.00');
        AppSetting::setValue('currency', 'د.ل');
        AppSetting::setValue('currency_code', 'LYD');
        AppSetting::setValue('towing_base_price', '50.00');
        AppSetting::setValue('app_name', 'GO FULL');
        AppSetting::setValue('support_phone', '0915909734');
    }
}
