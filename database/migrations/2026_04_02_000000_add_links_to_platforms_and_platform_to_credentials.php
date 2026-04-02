<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 🏗️ EXPANSÃO DA PLATAFORMA: Links de App
        Schema::table('platforms', function (Blueprint $table) {
            $table->string('app_android_url')->nullable()->after('url');
            $table->string('app_ios_url')->nullable()->after('app_android_url');
        });

        // 🏗️ EXPANSÃO DAS CREDENCIAIS: Vínculo com Plataforma
        Schema::table('customer_sub_users', function (Blueprint $table) {
            $table->foreignId('platform_id')->nullable()->after('customer_id')->constrained('platforms');
        });
    }

    public function down(): void
    {
        Schema::table('platforms', function (Blueprint $table) {
            $table->dropColumn(['app_android_url', 'app_ios_url']);
        });

        Schema::table('customer_sub_users', function (Blueprint $table) {
            $table->dropForeign(['platform_id']);
            $table->dropColumn('platform_id');
        });
    }
};
