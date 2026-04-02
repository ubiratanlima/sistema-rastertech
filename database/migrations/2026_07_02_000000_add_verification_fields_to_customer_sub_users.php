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
        Schema::table('customer_sub_users', function (Blueprint $table) {
            $table->timestamp('email_verified_at')->nullable();
            $table->string('validation_token')->nullable();
            $table->boolean('access_validated')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_sub_users', function (Blueprint $table) {
            $table->dropColumn(['email_verified_at', 'validation_token', 'access_validated']);
        });
    }
};
