<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-provider rejections: when a provider rejects a pending request,
     * it is hidden from THAT provider only — the request stays pending
     * so other providers can still accept it.
     */
    public function up(): void
    {
        Schema::create('request_rejections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('service_requests')->cascadeOnDelete();
            $table->foreignId('provider_id')->constrained('provider_profiles')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['request_id', 'provider_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_rejections');
    }
};
