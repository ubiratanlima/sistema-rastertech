<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'david@rastertech.com.br')->first();
if ($user) {
    echo "User: " . $user->email . "\n";
    echo "Role: '" . $user->role . "'\n";
} else {
    echo "User not found\n";
}
