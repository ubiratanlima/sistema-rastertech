<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;

try {
    echo "Iniciando migração...\n";
    Artisan::call('migrate', ['--force' => true]);
    echo Artisan::output();
    echo "\nMigração concluída com sucesso!";
} catch (\Exception $e) {
    echo "Erro na migração: " . $e->getMessage();
}
