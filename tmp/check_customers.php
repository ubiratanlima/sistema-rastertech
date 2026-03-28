<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$customers = \App\Models\Customer::limit(10)->get();
foreach ($customers as $c) {
    echo "ID: {$c->id} | Name: {$c->name} | Code: {$c->code}\n";
}
