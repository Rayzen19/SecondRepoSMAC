<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Foreign Keys Referencing strand_subjects ===\n\n";

$foreignKeys = DB::select("
    SELECT 
        TABLE_NAME,
        COLUMN_NAME,
        CONSTRAINT_NAME,
        REFERENCED_TABLE_NAME,
        REFERENCED_COLUMN_NAME
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE REFERENCED_TABLE_SCHEMA = DATABASE()
    AND REFERENCED_TABLE_NAME = 'strand_subjects'
");

foreach ($foreignKeys as $fk) {
    echo "Table: {$fk->TABLE_NAME}\n";
    echo "Column: {$fk->COLUMN_NAME}\n";
    echo "Constraint: {$fk->CONSTRAINT_NAME}\n";
    echo "References: {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
    echo "---\n";
}

echo "\n=== strand_subjects Table Structure ===\n\n";

$tableInfo = DB::select("SHOW CREATE TABLE strand_subjects");
echo $tableInfo[0]->{'Create Table'} . "\n";
