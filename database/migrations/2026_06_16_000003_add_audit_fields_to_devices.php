<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            if (!Schema::hasColumn('devices', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable();
            }
            if (!Schema::hasColumn('devices', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable();
            }
        });

        // 🛠️ ATUALIZANDO CONSTRAINT DE STATUS PARA ACEITAR 'canceled'
        // Em PostgreSQL, precisamos dropar a constraint antiga e criar a nova
        DB::statement("ALTER TABLE devices DROP CONSTRAINT IF EXISTS devices_status_check");
        DB::statement("ALTER TABLE devices ADD CONSTRAINT devices_status_check CHECK (status IN ('active', 'inactive', 'canceled'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['cancellation_reason', 'cancelled_at']);
        });
    }
};
