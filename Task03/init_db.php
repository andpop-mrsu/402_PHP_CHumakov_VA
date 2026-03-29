<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SakatoGin\Task03\Database;

Database::initialize();

echo "Database initialized successfully.\n";
