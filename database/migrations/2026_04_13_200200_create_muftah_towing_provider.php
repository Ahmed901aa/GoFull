<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        // Check if مفتاح صالح already exists as a provider
        $existing = DB::table('users')
            ->where('name', 'مفتاح صالح')
            ->where('role', 'provider')
            ->first();

        if ($existing) {
            // Just ensure provider_profile exists with towing type
            DB::table('provider_profiles')->updateOrInsert(
                ['user_id' => $existing->id],
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
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );

            return;
        }

        // Create مفتاح صالح as a provider user
        $userId = DB::table('users')->insertGetId([
            'name' => 'مفتاح صالح',
            'phone' => '0923664444',
            'password' => Hash::make('12345678'),
            'role' => 'provider',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create provider profile for towing
        DB::table('provider_profiles')->insert([
            'user_id' => $userId,
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
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        $user = DB::table('users')
            ->where('name', 'مفتاح صالح')
            ->where('phone', '0923664444')
            ->where('role', 'provider')
            ->first();

        if ($user) {
            DB::table('provider_profiles')->where('user_id', $user->id)->delete();
            DB::table('users')->where('id', $user->id)->delete();
        }
    }
};
