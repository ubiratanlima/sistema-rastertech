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
        Schema::create('vehicle_missions', function (Blueprint $row) {
            $row->id();
            $row->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $row->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $row->foreignId('driver_id')->constrained('portal_drivers')->onDelete('cascade');
            
            // Vínculos com os Checklists individuais
            $row->unsignedBigInteger('entry_id')->nullable();
            $row->unsignedBigInteger('exit_id')->nullable();
            
            $row->enum('status', ['open', 'closed'])->default('open');
            $row->timestamps();

            // Indexação para performance de busca
            $row->index(['customer_id', 'status']);
            $row->index('vehicle_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_missions');
    }
};
