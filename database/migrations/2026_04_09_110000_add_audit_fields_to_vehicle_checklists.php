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
        Schema::table('vehicle_checklists', function (Blueprint $table) {
            // 🛡️ ADIÇÃO DE RASTREABILIDADE E AUDITORIA
            $table->foreignId('customer_id')->nullable()->after('id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('performed_by_id')->nullable()->after('driver_id')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_checklists', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['performed_by_id']);
            $table->dropColumn(['customer_id', 'performed_by_id']);
        });
    }
};
