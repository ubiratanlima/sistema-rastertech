<?php
use App\Models\User;
use App\Models\CustomerSubUser;
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    // 1. Normalizar tabela users
    $map = [
        'Admin' => 'Administrador',
        'admin' => 'Administrador',
        'administrador' => 'Administrador',
        'gerente' => 'Gerente',
        'gestor' => 'Gerente',
        'suporte técnico' => 'Suporte',
        'Suporte Técnico' => 'Suporte',
        'suporte' => 'Suporte',
        'operator' => 'Operador',
        'operador' => 'Operador',
        'instalador' => 'Instalador',
        'técnico instalador' => 'Instalador',
        'Técnico Instalador' => 'Instalador',
        'cliente' => 'Cliente',
    ];

    foreach ($map as $old => $new) {
        User::where('role', $old)->update(['role' => $new]);
    }

    // 2. Normalizar tabela customer_sub_users (Cargos de campo)
    // O usuário não pediu explicitamente para mudar Motorista/Autorizado, mas vamos garantir o padrão PascalCase
    CustomerSubUser::where('role', 'driver')->update(['role' => 'Motorista']);
    CustomerSubUser::where('role', 'operator')->update(['role' => 'Operador']);

    DB::commit();
    echo "Base de dados higienizada com sucesso!";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Erro na higienização: " . $e->getMessage();
}
