<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CustomerSubUser;
use App\Models\User;

try {
    $subUser = CustomerSubUser::first(); // Pega o primeiro para teste
    if (!$subUser) {
        die("ERRO: Nenhum sub-usuário encontrado para teste.\n");
    }

    echo "Testando validação para: " . $subUser->name . "\n";
    
    $res = $subUser->update([
        'access_validated' => true,
        'email_verified_at' => now(),
        'validation_method' => 'manual'
    ]);

    if ($res) {
        echo "SUCESSO: Tabela customer_sub_users atualizada.\n";
    } else {
        echo "FALHA: O update retornou false (verifique fillable/dirty).\n";
    }

    $user = User::where('external_username', $subUser->external_username)->first();
    if ($user) {
        $resUser = $user->update(['access_validated' => true]);
        echo "SUCESSO: Usuário de login sincronizado.\n";
    } else {
        echo "AVISO: Usuário de login não encontrado para " . $subUser->external_username . "\n";
    }

} catch (\Exception $e) {
    echo "ERRO DE BANCO: " . $e->getMessage() . "\n";
}
