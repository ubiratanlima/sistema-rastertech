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
        Schema::create('vehicle_expenses', function (Blueprint $row) {
            $row->id();
            $row->foreignId('customer_id')->constrained();
            $row->foreignId('driver_id')->constrained('portal_drivers');
            $row->foreignId('vehicle_id')->constrained();
            
            // 🚥 TIPO DE OPERAÇÃO (ABASTECIMENTO, ÓLEO, OUTROS)
            $row->string('type'); // Abastecimento, Troca de Óleo, Outros Gastos
            $row->string('description')->nullable();
            
            // 📠 TELEMETRIA & FINANCEIRO
            $row->decimal('odometer', 15, 2);
            $row->decimal('amount', 12, 2); // Valor pago/lançado
            $row->decimal('fuel_liters', 10, 2)->nullable(); // Apenas em caso de combustível
            
            $row->string('receipt_photo')->nullable(); // Registro visual da nota (Opcional)
            $row->dateTime('date')->useCurrent();
            
            $row->softDeletes();
            $row->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_expenses');
    }
};
