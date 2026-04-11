<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fuel_prices', function (Blueprint $table) {
            $table->enum('tax_type', ['percentage', 'fixed'])->default('percentage')->after('price_per_liter');
            $table->decimal('tax_value', 8, 2)->default(0)->after('tax_type');
        });
    }

    public function down(): void
    {
        Schema::table('fuel_prices', function (Blueprint $table) {
            $table->dropColumn(['tax_type', 'tax_value']);
        });
    }
};
