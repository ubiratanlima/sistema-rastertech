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
            // 🚥 TESTES DE SINAL (BACKOFFICE)
            $table->boolean('test_online')->default(false)->after('checkout_notes');
            $table->boolean('test_block')->default(false)->after('test_online');
            $table->boolean('test_ignition_on')->default(false)->after('test_block');
            $table->boolean('test_ignition_off')->default(false)->after('test_ignition_on');
            
            // 🛡️ LOG DE AUDITORIA
            $table->unsignedBigInteger('validator_id')->nullable()->after('test_ignition_off');
            $table->dateTime('validated_at')->nullable()->after('validator_id');
            $table->text('validation_notes')->nullable()->after('validated_at');
            $table->string('validation_status')->default('pending')->after('validation_notes'); // pending, approved, rejected
            
            $table->foreign('validator_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('installations', function (Blueprint $table) {
            $table->dropForeign(['validator_id']);
            $table->dropColumn(['test_online', 'test_block', 'test_ignition_on', 'test_ignition_off', 'validator_id', 'validated_at', 'validation_notes', 'validation_status']);
        });
    }
};
