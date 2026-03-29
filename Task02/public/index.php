<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/Database.php';

use function SakatoGin\Calculator\initializeDatabase;

initializeDatabase();
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Calculator Game</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<div class="container">
    <h1>Calculator</h1>
    <p>
        Введите имя и начните игру. Вам будет предложено арифметическое выражение
        с четырьмя операндами.
    </p>

    <form action="/play.php" method="post" class="card">
        <label for="player_name">Имя игрока</label>
        <input
            type="text"
            id="player_name"
            name="player_name"
            required
            maxlength="100"
        >
        <button type="submit">Начать игру</button>
    </form>

    <p class="links">
        <a href="/history.php">История игр</a>
    </p>
</div>
</body>
</html>