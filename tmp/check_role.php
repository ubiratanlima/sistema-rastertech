<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$user = \App\Models\User::first();
if ($user) {
    echo "ID: {$user->id} | Name: {$user->name} | Role: {$user->role}\n";
}
