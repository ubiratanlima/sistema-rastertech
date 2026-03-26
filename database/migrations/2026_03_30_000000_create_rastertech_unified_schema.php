<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. FUNDAMENTO: Fornecedores
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->enum('type', ['hardware', 'connectivity', 'software'])->default('hardware');
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. CONECTIVIDADE: Plataformas (Sistemas)
        Schema::create('platforms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('url')->nullable();
            $table->string('server_ip', 45);
            $table->string('supplier_name')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. INTELIGÊNCIA: Modelos de Rastreadores
        Schema::create('device_models', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('manufacturer')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. CRM: Clientes e Veículos
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('document', 20)->nullable();
            $table->boolean('is_default_stock')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate', 10)->unique();
            $table->string('brand', 50)->nullable();
            $table->string('model', 50)->nullable();
            $table->foreignId('customer_id')->constrained('customers');
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. INFRAESTRUTURA: Chips (GsmCard)
        Schema::create('gsm_cards', function (Blueprint $table) {
            $table->id();
            $table->string('iccid', 30)->unique();
            $table->string('phone_number', 20)->nullable();
            $table->string('operator', 50)->nullable();
            $table->string('apn', 100)->nullable();
            $table->string('apn_user', 50)->nullable();
            $table->string('apn_pass', 50)->nullable();
            $table->foreignId('provider_id')->nullable()->constrained('providers');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        // 6. CORE: Rastreadores (Devices)
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('imei', 15)->unique();
            $table->string('model_description', 100)->nullable();
            
            $table->foreignId('device_model_id')->nullable()->constrained('device_models');
            $table->foreignId('platform_id')->nullable()->constrained('platforms');
            $table->string('port_number', 10)->nullable();
            $table->foreignId('gsm_card_id')->nullable()->unique()->constrained('gsm_cards');
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles');
            $table->foreignId('provider_id')->nullable()->constrained('providers');

            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        // 7. AUTOMAÇÃO: Comandos SMS
        Schema::create('device_commands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_model_id')->constrained('device_models')->onDelete('cascade');
            $table->string('description', 100);
            $table->text('command_template');
            $table->integer('execution_order')->default(1);
            $table->timestamps();
        });

        // 8. ACESSOS: Sub-Usuários do Cliente
        Schema::create('customer_sub_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->string('name', 100);
            $table->enum('role', ['operator', 'driver'])->default('operator');
            $table->json('permissions')->nullable();
            $table->string('external_username')->nullable();
            $table->string('external_password')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_sub_users');
        Schema::dropIfExists('device_commands');
        Schema::dropIfExists('devices');
        Schema::dropIfExists('gsm_cards');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('device_models');
        Schema::dropIfExists('platforms');
        Schema::dropIfExists('providers');
    }
};
