<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Game.php';
require_once __DIR__ . '/../src/Database.php';

use function SakatoGin\Calculator\generateRound;
use function SakatoGin\Calculator\initializeDatabase;

initializeDatabase();

$playerName = trim($_POST['player_name'] ?? '');

if ($playerName === '') {
    header('Location: /');
    exit;
}

$round = generateRound();
$expression = $round['expression'];
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

    <div class="card">
        <p><strong>Игрок:</strong> <?= htmlspecialchars($playerName) ?></p>
        <p><strong>Выражение:</strong> <?= htmlspecialchars($expression) ?></p>
    </div>

    <form action="/result.php" method="post" class="card">
        <input
            type="hidden"
            name="player_name"
            value="<?= htmlspecialchars($playerName) ?>"
        >
        <input
            type="hidden"
            name="expression"
            value="<?= htmlspecialchars($expression) ?>"
        >

        <label for="user_answer">Ваш ответ</label>
        <input
            type="number"
            id="user_answer"
            name="user_answer"
            required
        >

        <button type="submit">Проверить</button>
    </form>

    <p class="links">
        <a href="/">На главную</a>
        <a href="/history.php">История игр</a>
    </p>
</div>
</body>
</html>