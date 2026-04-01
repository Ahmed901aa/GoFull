<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provider_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->unique()
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->enum('service_type', ['fuel_delivery', 'towing']);
            $table->string('vehicle_make', 100);
            $table->string('vehicle_model', 100);
            $table->year('vehicle_year');
            $table->string('vehicle_plate', 20)->unique();
            $table->string('vehicle_color', 50)->nullable();
            $table->boolean('is_available')->default(false);
            $table->enum('verification_status', [
                'pending',
                'appointment_set',
                'approved',
                'rejected'
            ])->default('pending');
            $table->dateTime('appointment_date')->nullable();
            $table->text('appointment_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->unsignedInteger('total_ratings')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_profiles');
    }
};