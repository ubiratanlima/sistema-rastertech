<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$table = 'customer_sub_users';
$columns = ['validated_by', 'validation_method'];
$missing = [];

foreach ($columns as $column) {
    if (!Schema::hasColumn($table, $column)) {
        $missing[] = $column;
    }
}

if (empty($missing)) {
    echo "SUCCESS: All columns exist.\n";
} else {
    echo "ERROR: Missing columns: " . implode(', ', $missing) . "\n";
    echo "Please run: php artisan migrate\n";
}
