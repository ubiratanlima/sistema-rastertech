<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$migration = '2026_08_03_310000_add_validation_audit_to_customer_sub_users';
$exists = DB::table('migrations')->where('migration', $migration)->exists();

if ($exists) {
    echo "MIGRATION_EXISTS\n";
} else {
    echo "MIGRATION_MISSING\n";
}
