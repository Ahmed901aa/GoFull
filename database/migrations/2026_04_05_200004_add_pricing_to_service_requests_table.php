<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->decimal('price_per_liter', 8, 2)->nullable()->after('fuel_quantity');
            $table->decimal('subtotal', 10, 2)->nullable()->after('notes');
            $table->decimal('service_fee', 8, 2)->nullable()->after('subtotal');
            $table->decimal('total', 10, 2)->nullable()->after('service_fee');
            $table->enum('payment_method', ['cash'])->default('cash')->after('total');
            $table->enum('payment_status', ['pending', 'paid'])->default('pending')->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn(['price_per_liter', 'subtotal', 'service_fee', 'total', 'payment_method', 'payment_status']);
        });
    }
};
