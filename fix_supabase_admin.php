<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

try {
    echo "🐘 TENTANDO CRIAR USUÁRIO SUPABASE_ADMIN...\n";
    
    $password = env('DB_PASSWORD', '567099a56bbd02ecaaff74dbe4edd2ca');
    
    // Verifica se já existe
    $exists = DB::select("SELECT 1 FROM pg_roles WHERE rolname = 'supabase_admin'");
    
    if (empty($exists)) {
        DB::statement("CREATE USER supabase_admin WITH PASSWORD '$password' SUPERUSER");
        echo "✅ USUÁRIO CRIADO COM SUCESSO!\n";
    } else {
        DB::statement("ALTER USER supabase_admin WITH PASSWORD '$password' SUPERUSER");
        echo "♻️ USUÁRIO JÁ EXISTIA, SENHA ATUALIZADA!\n";
    }

} catch (\Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
