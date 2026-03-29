<?php

declare(strict_types=1);

require_once __DIR__ . '/src/Database.php';

use function SakatoGin\Calculator\initializeDatabase;

initializeDatabase();

echo "Database initialized successfully.\n";