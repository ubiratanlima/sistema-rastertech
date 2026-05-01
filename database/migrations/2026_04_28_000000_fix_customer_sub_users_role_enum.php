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
        // No Postgres, precisamos remover a restrição de enum para permitir novos valores ou mudar para string
        // Primeiro, tentamos converter a coluna para string para remover a rigidez do ENUM
        DB::statement("ALTER TABLE customer_sub_users ALTER COLUMN role TYPE VARCHAR(50)");
        
        // Removemos a constraint de check que o Laravel/Postgres cria automaticamente para Enums
        DB::statement("ALTER TABLE customer_sub_users DROP CONSTRAINT IF EXISTS customer_sub_users_role_check");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Caso precise voltar, retornamos para enum (opcional, string é mais seguro)
        DB::statement("ALTER TABLE customer_sub_users ALTER COLUMN role TYPE VARCHAR(255)");
    }
};
