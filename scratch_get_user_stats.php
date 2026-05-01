<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$results = DB::table('users')
    ->select('role', DB::raw('count(*) as total'))
    ->groupBy('role')
    ->get();

echo json_encode($results, JSON_PRETTY_PRINT);
