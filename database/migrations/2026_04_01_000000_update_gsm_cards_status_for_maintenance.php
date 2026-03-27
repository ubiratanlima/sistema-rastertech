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
        // Nota: Em SQLite (usado no ambiente de dev atual), enums são tratados como strings.
        // Apenas documentamos a intenção do novo valor 'maintenance'.
        Schema::table('gsm_cards', function (Blueprint $table) {
            // No MySQL, usaríamos: $table->enum('status', ['active', 'inactive', 'suspended', 'maintenance'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter status se necessário.
    }
};
