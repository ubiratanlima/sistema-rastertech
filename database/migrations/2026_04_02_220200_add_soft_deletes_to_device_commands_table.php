<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona suporte a SoftDeletes na tabela de comandos.
     */
    public function up()
    {
        Schema::table('device_commands', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Remove o suporte a SoftDeletes.
     */
    public function down()
    {
        Schema::table('device_commands', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
