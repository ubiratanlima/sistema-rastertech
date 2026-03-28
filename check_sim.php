<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$sim = App\Models\GsmCard::withTrashed()->find(495);
if ($sim) {
    echo "ID: " . $sim->id . "\n";
    echo "Status: " . $sim->status . "\n";
    echo "Deleted At: " . ($sim->deleted_at ? $sim->deleted_at : 'NULL') . "\n";
    echo "Is Trashed: " . ($sim->trashed() ? 'YES' : 'NO') . "\n";
} else {
    echo "ID 495 NOT FOUND in DB (Even withTrashed)\n";
}

$allInactives = App\Models\GsmCard::withTrashed()->where(function($q) {
    $q->where('status', '!=', 'active')->orWhereNotNull('deleted_at');
})->count();
echo "Total Inactives in DB: " . $allInactives . "\n";
