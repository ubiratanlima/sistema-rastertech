<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 🛠️ ATUALIZANDO CONSTRAINT DE STATUS PARA ACEITAR 'canceled'
        // Em PostgreSQL, precisamos dropar a constraint antiga e criar a nova
        DB::statement("ALTER TABLE gsm_cards DROP CONSTRAINT IF EXISTS gsm_cards_status_check");
        DB::statement("ALTER TABLE gsm_cards ADD CONSTRAINT gsm_cards_status_check CHECK (status IN ('active', 'inactive', 'canceled'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE gsm_cards DROP CONSTRAINT IF EXISTS gsm_cards_status_check");
        DB::statement("ALTER TABLE gsm_cards ADD CONSTRAINT gsm_cards_status_check CHECK (status IN ('active', 'inactive'))");
    }
};
