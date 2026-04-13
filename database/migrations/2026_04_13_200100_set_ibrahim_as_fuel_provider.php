<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Set ابراهيم صالح as fuel provider
        DB::table('provider_profiles')
            ->join('users', 'users.id', '=', 'provider_profiles.user_id')
            ->where('users.name', 'ابراهيم صالح')
            ->update(['provider_profiles.service_type' => 'fuel_delivery']);
    }

    public function down(): void
    {
        // No rollback needed
    }
};
