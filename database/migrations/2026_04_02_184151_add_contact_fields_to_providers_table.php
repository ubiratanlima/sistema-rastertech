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
        Schema::table('providers', function (Blueprint $table) {
            $table->string('email', 150)->nullable()->after('type');
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('document', 20)->nullable()->after('phone');
            $table->string('contact_name', 100)->nullable()->after('document');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->dropColumn(['email', 'phone', 'document', 'contact_name']);
        });
    }
};
