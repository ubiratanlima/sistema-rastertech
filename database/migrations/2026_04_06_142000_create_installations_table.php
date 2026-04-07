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
        Schema::create('installations', function (Blueprint $row) {
            $row->id();
            $row->foreignId('installer_id')->constrained('users'); // Técnico
            
            // 🚛 DADOS DO VEÍCULO E CLIENTE
            $row->string('customer_name')->nullable();
            $row->string('vehicle_plate')->nullable();
            $row->text('vehicle_details')->nullable();
            
            // 🚥 ESTADO DA INSTALAÇÃO
            $row->string('status')->default('pending'); // pending, ongoing, completed
            $row->dateTime('checkin_at')->nullable();
            
            // 📸 VISTORIA VISUAL (JSON PARA SLOTS 1-10)
            $row->json('photos')->nullable();
            $row->json('extra_photos')->nullable();
            $row->string('customer_id_photo')->nullable(); // Foto do Documento (RG/CNH)
            
            $row->softDeletes();
            $row->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installations');
    }
};
