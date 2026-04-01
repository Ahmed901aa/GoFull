<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provider_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')
                  ->constrained('provider_profiles')
                  ->cascadeOnDelete();
            $table->enum('document_type', [
                'national_id',
                'driving_license',
                'vehicle_license',
                'vehicle_photo',
                'insurance'
            ]);
            $table->string('document_path');
            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_documents');
    }
};