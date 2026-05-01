<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CustomerSubUser;
use App\Models\PortalDriver;

echo "CustomerSubUser count: " . CustomerSubUser::count() . "\n";
echo "CustomerSubUser roles: " . CustomerSubUser::select('role')->distinct()->get()->pluck('role')->implode(', ') . "\n";
echo "PortalDriver count: " . PortalDriver::count() . "\n";
echo "PortalDriver with sub_user_id: " . PortalDriver::whereNotNull('sub_user_id')->count() . "\n";
