<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('provider_id')
                  ->nullable()
                  ->constrained('provider_profiles')
                  ->nullOnDelete();
            $table->enum('service_type', ['fuel_delivery', 'towing']);
            $table->enum('status', [
                'pending',
                'accepted',
                'en_route',
                'arrived',
                'in_progress',
                'completed',
                'cancelled'
            ])->default('pending');
            $table->decimal('driver_latitude', 10, 8);
            $table->decimal('driver_longitude', 11, 8);
            $table->string('driver_address')->nullable();
            $table->text('notes')->nullable();

            // حقول خاصة بـ fuel_delivery فقط
            $table->enum('fuel_type', ['petrol', 'diesel'])->nullable();
            $table->decimal('fuel_quantity', 5, 2)->nullable();

            // حقول خاصة بـ towing فقط
            $table->string('plate_number', 20)->nullable();

            // timestamps للحالات
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->enum('cancelled_by', ['driver', 'provider', 'admin'])->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};