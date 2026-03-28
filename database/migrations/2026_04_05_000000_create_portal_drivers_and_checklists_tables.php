<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. GESTÃO DE MOTORISTAS (DADOS TÉCNICOS E CNH)
        Schema::create('portal_drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('sub_user_id')->nullable()->constrained('customer_sub_users')->onDelete('set null');
            $table->string('name', 150);
            $table->string('cnh_number', 20)->unique();
            $table->date('cnh_expiry');
            $table->string('cnh_front_path')->nullable();
            $table->string('cnh_back_path')->nullable();
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active');
            $table->timestamp('last_checklist_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. CHECKLISTS OPERACIONAIS (ENTRADA E SAÍDA)
        Schema::create('vehicle_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('driver_id')->constrained('portal_drivers')->onDelete('cascade');
            $table->enum('type', ['entry', 'exit']);
            $table->integer('odometer')->nullable();
            $table->string('fuel_level', 20)->nullable();
            $table->json('photos')->nullable(); // Caminhos das fotos de avarias/estado
            $table->text('notes')->nullable(); // Campo de mensagem para relatar problemas
            $table->timestamps();
        });

        // 3. CANAIS DE SUPORTE DO CLIENTE (WHATSAPP SECUNDÁRIOS)
        Schema::create('customer_whatsapp_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('whatsapp_number', 25);
            $table->string('contact_name', 100)->nullable();
            $table->string('label', 50)->nullable(); // Ex: Logística, Gerência, Emergência
            $table->timestamps();
        });

        // 4. PERSONALIZAÇÃO DE PERFIL
        Schema::table('customer_sub_users', function (Blueprint $table) {
            $table->string('nickname', 50)->after('name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('customer_sub_users', function (Blueprint $table) {
            $table->dropColumn('nickname');
        });
        Schema::dropIfExists('customer_whatsapp_numbers');
        Schema::dropIfExists('vehicle_checklists');
        Schema::dropIfExists('portal_drivers');
    }
};
