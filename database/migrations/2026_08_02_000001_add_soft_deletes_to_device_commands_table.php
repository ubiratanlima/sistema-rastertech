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
        if (!Schema::hasColumn('device_commands', 'deleted_at')) {
            Schema::table('device_commands', function (Blueprint $row) {
                $row->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('device_commands', 'deleted_at')) {
            Schema::table('device_commands', function (Blueprint $row) {
                $row->dropSoftDeletes();
            });
        }
    }
};
