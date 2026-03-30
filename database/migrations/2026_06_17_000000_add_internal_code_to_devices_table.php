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
        // 1. ADICIONA O CAMPO COMO NULLABLE PARA PERMITIR REGISTROS EXISTENTES
        Schema::table('devices', function (Blueprint $table) {
            $table->string('internal_code')->nullable()->after('imei');
        });

        // 2. POPULA OS DADOS EXISTENTES EM SEQUÊNCIA (PADRÃO RTECH-XXXXX)
        $devices = DB::table('devices')->orderBy('id', 'asc')->get();
        $counter = 1;
        
        foreach ($devices as $device) {
            $code = 'RTECH-' . str_pad($counter++, 5, '0', STR_PAD_LEFT);
            DB::table('devices')
                ->where('id', $device->id)
                ->update(['internal_code' => $code]);
        }

        // 3. TORNA O CAMPO OBRIGATÓRIO E ÚNICO
        Schema::table('devices', function (Blueprint $table) {
            $table->string('internal_code')->unique()->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('internal_code');
        });
    }
};
