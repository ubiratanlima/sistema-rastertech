<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Garantir que as colunas role sejam VARCHAR para evitar problemas com ENUM legado no Postgres
        DB::statement("ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(100)");
        DB::statement("ALTER TABLE customer_sub_users ALTER COLUMN role TYPE VARCHAR(100)");

        // 2. Mapeamento de Unificação (Fusão de Operador -> Suporte)
        $mappings = [
            'users' => [
                'Admin'             => 'Administrador',
                'admin'             => 'Administrador',
                'administrador'     => 'Administrador',
                'Gerente'           => 'Gerente',
                'gerente'           => 'Gerente',
                'gestor'            => 'Gerente',
                'Suporte'           => 'Suporte',
                'suporte'           => 'Suporte',
                'suporte técnico'   => 'Suporte',
                'Suporte Técnico'   => 'Suporte',
                'operator'          => 'Suporte',
                'operador'          => 'Suporte',
                'Operador'          => 'Suporte',
                'operator'          => 'Suporte',
                'Instalador'        => 'Instalador',
                'instalador'        => 'Instalador',
                'técnico instalador' => 'Instalador',
                'Técnico Instalador' => 'Instalador',
                'Cliente'           => 'Cliente',
                'cliente'           => 'Cliente',
                'driver'            => 'Motorista',
                'motorista'         => 'Motorista',
                'Motorista'         => 'Motorista',
            ],
            'customer_sub_users' => [
                'operator'   => 'Suporte',
                'operador'   => 'Suporte',
                'Operador'   => 'Suporte',
                'driver'     => 'Motorista',
                'motorista'  => 'Motorista',
                'Motorista'  => 'Motorista',
                'autorizado' => 'Autorizado',
                'Autorizado' => 'Autorizado',
            ]
        ];

        foreach ($mappings as $table => $map) {
            foreach ($map as $old => $new) {
                DB::table($table)->where('role', $old)->update(['role' => $new]);
            }
        }

        // 3. Definir novos padrões (Defaults)
        DB::statement("ALTER TABLE customer_sub_users ALTER COLUMN role SET DEFAULT 'Suporte'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não há retorno para a bagunça antiga
    }
};
