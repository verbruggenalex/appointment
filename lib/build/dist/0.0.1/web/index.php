<?php

$databaseName = str_replace(['/var/www/html/', '/web', '/', '.'], ['', '', '_', '-'], __DIR__);

echo "Database name: " . $databaseName;

phpinfo();
