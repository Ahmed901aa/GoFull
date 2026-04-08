<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->decimal('destination_latitude', 10, 8)->nullable()->after('driver_address');
            $table->decimal('destination_longitude', 11, 8)->nullable()->after('destination_latitude');
            $table->string('destination_address')->nullable()->after('destination_longitude');
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn(['destination_latitude', 'destination_longitude', 'destination_address']);
        });
    }
};
