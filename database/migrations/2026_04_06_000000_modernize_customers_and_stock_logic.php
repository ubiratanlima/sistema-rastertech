<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 🏗️ 1. MODERNIZAÇÃO DA TABELA CUSTOMERS (MAPEAMENTO DO PRINT)
        Schema::table('customers', function (Blueprint $table) {
            // Removendo lógica antiga de estoque
            if (Schema::hasColumn('customers', 'is_default_stock')) {
                $table->dropColumn('is_default_stock');
            }

            // COMUNICAÇÃO UNIFICADA (Posição 0 = Principal)
            $table->string('company_name')->nullable()->after('name');
            $table->json('email')->nullable()->after('company_name');
            $table->string('cell_phone', 25)->nullable();
            $table->string('landline_phone', 25)->nullable();
            
            // Endereço (Mantendo padrão screenshot)
            $table->string('zip_code', 15)->nullable();
            $table->string('street')->nullable();
            $table->string('number', 20)->nullable();
            $table->string('complement')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('city')->nullable();
            
            // CRM
            $table->text('notes')->nullable();
        });

        // 🛰️ 2. MODERNIZAÇÃO DA LÓGICA DE ESTOQUE (DEVICES / CHIPS)
        // Se customer_id for NULL, o item é do estoque automático.
        Schema::table('devices', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->change();
        });
        
        // Limpeza de Legado: Se houver algum dispositivo no cliente "Estoque Geral", remover vínculo
        DB::table('devices')->whereIn('customer_id', function($q) {
            $q->select('id')->from('customers')->where('name', 'LIKE', '%ESTOQUE GERAL%');
        })->update(['customer_id' => null]);

        DB::table('gsm_cards')->whereIn('customer_id', function($q) {
            $q->select('id')->from('customers')->where('name', 'LIKE', '%ESTOQUE GERAL%');
        })->update(['customer_id' => null]);
        
        // Remover Clientes Pseudo-Estoque
        DB::table('customers')->where('name', 'LIKE', '%ESTOQUE GERAL%')->delete();
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'company_name', 'email', 'cell_phone', 'landline_phone',
                'zip_code', 'street', 'number', 'complement', 'neighborhood', 'city',
                'send_boletos_postal', 'additional_emails', 'notes'
            ]);
            $table->boolean('is_default_stock')->default(false);
        });

        Schema::table('devices', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable(false)->change();
        });
    }
};
