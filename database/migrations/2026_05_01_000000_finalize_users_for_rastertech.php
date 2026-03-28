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
        Schema::table('users', function (Blueprint $table) {
            // Vínculo com o Cliente
            if (!Schema::hasColumn('users', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->after('id')->constrained('customers')->onDelete('set null');
            }
            // Credenciais Externas
            if (!Schema::hasColumn('users', 'external_username')) {
                $table->string('external_username')->unique()->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'external_password')) {
                $table->string('external_password')->nullable()->after('external_username');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_id');
            $table->dropColumn(['external_username', 'external_password']);
        });
    }
};
