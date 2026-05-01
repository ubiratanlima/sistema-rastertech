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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('year', 4)->nullable()->after('model');
            $table->string('color', 30)->nullable()->after('year');
            $table->string('renavam', 20)->nullable()->after('color');
            $table->string('chassi', 30)->nullable()->after('renavam');
            $table->string('photo_front')->nullable()->after('chassi');
            $table->string('photo_back')->nullable()->after('photo_front');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['year', 'color', 'renavam', 'chassi', 'photo_front', 'photo_back']);
        });
    }
};
