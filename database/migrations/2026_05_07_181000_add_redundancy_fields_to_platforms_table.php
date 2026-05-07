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
        Schema::table('platforms', function (Blueprint $table) {
            $table->string('server_ip2', 45)->nullable()->after('server_ip');
            $table->string('dns1', 255)->nullable()->after('server_ip2');
            $table->string('dns2', 255)->nullable()->after('dns1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('platforms', function (Blueprint $table) {
            $table->dropColumn(['server_ip2', 'dns1', 'dns2']);
        });
    }
};
