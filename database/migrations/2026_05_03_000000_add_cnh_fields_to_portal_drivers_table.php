<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 🚥 EXPANSÃO TÁTICA: INJETANDO CAMPOS REAIS DA CNH
        Schema::table('portal_drivers', function (Blueprint $table) {
            if (!Schema::hasColumn('portal_drivers', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('name');
            }
            if (!Schema::hasColumn('portal_drivers', 'birth_place')) {
                $table->string('birth_place', 100)->nullable()->after('birth_date');
            }
            if (!Schema::hasColumn('portal_drivers', 'issue_date')) {
                $table->date('issue_date')->nullable()->after('cnh_number');
            }
            if (!Schema::hasColumn('portal_drivers', 'cpf')) {
                $table->string('cpf', 20)->nullable()->after('birth_place');
            }
            if (!Schema::hasColumn('portal_drivers', 'rg')) {
                $table->string('rg', 20)->nullable()->after('cpf');
            }
            if (!Schema::hasColumn('portal_drivers', 'issuer')) {
                $table->string('issuer', 50)->nullable()->after('rg'); 
            }
            if (!Schema::hasColumn('portal_drivers', 'uf')) {
                $table->string('uf', 2)->nullable()->after('issuer');
            }
            if (!Schema::hasColumn('portal_drivers', 'category')) {
                $table->string('category', 5)->nullable()->after('cnh_expiry');
            }
            if (!Schema::hasColumn('portal_drivers', 'father_name')) {
                $table->string('father_name', 150)->nullable()->after('name');
            }
            if (!Schema::hasColumn('portal_drivers', 'mother_name')) {
                $table->string('mother_name', 150)->nullable()->after('father_name');
            }
            if (!Schema::hasColumn('portal_drivers', 'nationality')) {
                $table->string('nationality', 50)->nullable()->after('birth_place');
            }
            if (!Schema::hasColumn('portal_drivers', 'territory_validity')) {
                $table->string('territory_validity')->nullable()->after('cnh_expiry');
            }
        });
    }

    public function down(): void
    {
        Schema::table('portal_drivers', function (Blueprint $table) {
            $table->dropColumn([
                'birth_date', 'birth_place', 'issue_date', 'cpf', 'rg', 
                'issuer', 'uf', 'category', 'father_name', 'mother_name', 
                'nationality', 'territory_validity'
            ]);
        });
    }
};
