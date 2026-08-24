<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * status is filtered in nearly every list/exists query (pending feeds,
 * active-order checks, history, admin monitor). The composite indexes
 * serve the per-user "has active order" checks directly.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->index('status');
            $table->index(['driver_id', 'status']);
            $table->index(['provider_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['driver_id', 'status']);
            $table->dropIndex(['provider_id', 'status']);
        });
    }
};
