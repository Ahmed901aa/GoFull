<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Allow 'system' as a canceller — used by the orders:expire command
     * when no provider accepts a request within the timeout window.
     */
    public function up(): void
    {
        if (in_array(Schema::getConnection()->getDriverName(), ['mysql', 'mariadb'])) {
            DB::statement("ALTER TABLE service_requests MODIFY cancelled_by ENUM('driver', 'provider', 'admin', 'system') NULL");
        }
        // SQLite (local dev): stores enums as TEXT with a CHECK constraint that
        // Laravel doesn't enforce on ALTER — no action needed.
    }

    public function down(): void
    {
        if (in_array(Schema::getConnection()->getDriverName(), ['mysql', 'mariadb'])) {
            DB::statement("ALTER TABLE service_requests MODIFY cancelled_by ENUM('driver', 'provider', 'admin') NULL");
        }
    }
};
