<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The ratings table held ONE row per request, shared by both rating
 * directions — so a provider rating the customer overwrote the customer's
 * rating of the provider (and vice versa). Split the directions:
 *   rated_by = 'driver'   → the customer's rating OF the provider
 *   rated_by = 'provider' → the provider's rating OF the customer
 * Existing rows were all written through the driver flow → default 'driver'.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->enum('rated_by', ['driver', 'provider'])
                  ->default('driver')
                  ->after('request_id');
            // Composite unique first: it starts with request_id, so it also
            // satisfies the FK index requirement, letting us drop the old
            // single-column unique safely on MySQL.
            $table->unique(['request_id', 'rated_by']);
            $table->dropUnique(['request_id']);
        });
    }

    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->unique(['request_id']);
            $table->dropUnique(['request_id', 'rated_by']);
            $table->dropColumn('rated_by');
        });
    }
};
