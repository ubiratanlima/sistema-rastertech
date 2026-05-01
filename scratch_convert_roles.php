<?php
use App\Models\CustomerSubUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    // 1. Converter na tabela customer_sub_users
    $opsCount = CustomerSubUser::where('role', 'operator')->update(['role' => 'Autorizado']);
    $driversCount = CustomerSubUser::where('role', 'driver')->update(['role' => 'Motorista']);

    echo "Tabela customer_sub_users:<br>";
    echo "- $opsCount operadores convertidos para Autorizado.<br>";
    echo "- $driversCount motoristas convertidos para Motorista.<br><br>";

    // 2. Sincronizar na tabela users
    // Buscamos todos os sub-usuários e forçamos a atualização no users para garantir integridade
    $subUsers = CustomerSubUser::all();
    $usersSyncCount = 0;

    foreach ($subUsers as $sub) {
        $updated = User::where('external_username', $sub->external_username)
            ->update(['role' => $sub->role]);
        if ($updated) $usersSyncCount++;
    }

    echo "Tabela users:<br>";
    echo "- $usersSyncCount registros sincronizados com os novos cargos.<br>";

    DB::commit();
    echo "<br>Conversão concluída com sucesso!";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Erro na conversão: " . $e->getMessage();
}
