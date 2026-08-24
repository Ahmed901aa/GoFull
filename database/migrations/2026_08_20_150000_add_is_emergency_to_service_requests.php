<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Emergency fuel is an ATTRIBUTE of a fuel order, not a separate lifecycle:
 * same statuses, same provider pool. The flag drives dispatch priority,
 * a shorter expiry window, urgent notification copy, and UI badges.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->boolean('is_emergency')->default(false)->after('service_type');
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn('is_emergency');
        });
    }
};
