<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Banners now drive two home-page sections in the app:
     *   - type 'promo'   → big photo banners (stations, offers)
     *   - type 'service' → service grid tiles (fuel, towing, emergency)
     * `action` tells the app what to open when a service tile is tapped
     * (fuel | towing | emergency).
     */
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('type', 20)->default('promo')->after('title');
            $table->string('action', 30)->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['type', 'action']);
        });
    }
};
