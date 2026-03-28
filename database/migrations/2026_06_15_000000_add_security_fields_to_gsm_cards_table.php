<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gsm_cards', function (Blueprint $table) {
            $table->string('pin', 10)->nullable()->after('apn_pass');
            $table->string('puk', 20)->nullable()->after('pin');
            $table->string('pin2', 10)->nullable()->after('puk');
            $table->string('puk2', 20)->nullable()->after('pin2');
        });
    }

    public function down(): void
    {
        Schema::table('gsm_cards', function (Blueprint $table) {
            $table->dropColumn(['pin', 'puk', 'pin2', 'puk2']);
        });
    }
};
