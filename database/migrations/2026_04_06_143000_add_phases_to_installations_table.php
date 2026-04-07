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
        Schema::table('installations', function (Blueprint $table) {
            // Renomear a coluna antiga de fotos para clareza (Check-in)
            if (Schema::hasColumn('installations', 'photos')) {
                $table->renameColumn('photos', 'checkin_photos');
            }

            // Injetar novas colunas táticas
            $table->json('process_photos')->nullable()->after('checkin_photos'); // Parte Elétrica (Fase 2)
            $table->json('checkout_photos')->nullable()->after('process_photos'); // Finalização (Fase 3)
            $table->text('checkout_notes')->nullable()->after('checkout_photos'); // Relato do Técnico
            
            // Timestamps de Controle de Fluxo
            $table->dateTime('processed_at')->nullable()->after('checkin_at');
            $table->dateTime('completed_at')->nullable()->after('processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('installations', function (Blueprint $table) {
            $table->dropColumn(['process_photos', 'checkout_photos', 'checkout_notes', 'processed_at', 'completed_at']);
        });
    }
};
