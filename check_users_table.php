<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Users Table Structure:\n";
echo "========================================\n";

$columns = DB::select('DESCRIBE users');
foreach($columns as $col) {
    echo "{$col->Field} ({$col->Type})\n";
}

echo "\n\nSample User Record:\n";
echo "========================================\n";
$user = DB::table('users')->first();
if ($user) {
    print_r($user);
}
