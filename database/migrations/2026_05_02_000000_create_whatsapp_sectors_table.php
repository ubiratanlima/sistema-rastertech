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
        if (!Schema::hasTable('whatsapp_sectors')) {
            Schema::create('whatsapp_sectors', function (Blueprint $table) {
                $table->id();
                $table->string('name', 50)->unique();
                $table->timestamps();
            });
            
            // Popular iniciais táticos
            $sectors = ['ATENDIMENTO', 'COMERCIAL', 'GERÊNCIA', 'LOGÍSTICA', 'ADMINISTRAÇÃO', 'RECURSOS HUMANOS'];
            foreach ($sectors as $s) {
                \Illuminate\Support\Facades\DB::table('whatsapp_sectors')->insertOrIgnore([
                    'name' => $s,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_sectors');
    }
};
