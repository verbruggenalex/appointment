<?php

$databaseName = str_replace(['/var/www/html/', '/web'], '', __DIR__);
$databaseName = str_replace('/', '_', $databaseName);
$databaseName = str_replace('.', '-', $databaseName);

echo "Database name: " . $databaseName;

phpinfo();
