<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('customer_sub_users', function (Blueprint $table) {
            $table->unsignedBigInteger('validated_by')->nullable()->after('access_validated');
            $table->string('validation_method')->nullable()->after('validated_by'); // 'email' or 'manual'
            
            $table->foreign('validated_by')->references('id')->on('users')->onDelete('set null');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('validated_by')->nullable()->after('access_validated');
            $table->string('validation_method')->nullable()->after('validated_by');
            
            $table->foreign('validated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('customer_sub_users', function (Blueprint $table) {
            $table->dropForeign(['validated_by']);
            $table->dropColumn(['validated_by', 'validation_method']);
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['validated_by']);
            $table->dropColumn(['validated_by', 'validation_method']);
        });
    }
};
