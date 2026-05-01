<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$table = 'customer_sub_users';
$columns = Schema::getColumnListing($table);

echo "COLUMNS: " . implode(', ', $columns) . "\n";
