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
        Schema::table('gsm_cards', function (Blueprint $row) {
            $row->text('cancellation_reason')->nullable()->after('status');
            $row->timestamp('cancelled_at')->nullable()->after('cancellation_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gsm_cards', function (Blueprint $row) {
            $row->dropColumn(['cancellation_reason', 'cancelled_at']);
        });
    }
};
