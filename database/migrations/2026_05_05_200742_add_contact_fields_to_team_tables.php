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
        Schema::table('portal_drivers', function (Blueprint $table) {
            $table->string('email')->nullable()->after('rg');
            $table->string('whatsapp')->nullable()->after('email');
        });

        Schema::table('customer_sub_users', function (Blueprint $table) {
            $table->string('email')->nullable()->after('external_username');
            $table->string('whatsapp')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portal_drivers', function (Blueprint $table) {
            $table->dropColumn(['email', 'whatsapp']);
        });

        Schema::table('customer_sub_users', function (Blueprint $table) {
            $table->dropColumn(['email', 'whatsapp']);
        });
    }
};
