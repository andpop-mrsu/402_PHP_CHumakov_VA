<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Game.php';
require_once __DIR__ . '/../src/Database.php';

use function SakatoGin\Calculator\evaluateExpressionFromString;
use function SakatoGin\Calculator\initializeDatabase;
use function SakatoGin\Calculator\saveGame;

initializeDatabase();

$playerName = trim($_POST['player_name'] ?? '');
$expression = trim($_POST['expression'] ?? '');
$userAnswer = trim($_POST['user_answer'] ?? '');

if ($playerName === '' || $expression === '' || $userAnswer === '') {
    header('Location: /');
    exit;
}

$correctAnswer = evaluateExpressionFromString($expression);
$isCorrect = ((string) $correctAnswer === $userAnswer) ? 1 : 0;
$playedAt = date('Y-m-d H:i:s');

saveGame(
    $playerName,
    $playedAt,
    $expression,
    $correctAnswer,
    $userAnswer,
    $isCorrect
);
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Результат игры</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<div class="container">
    <h1>Результат игры</h1>

    <div class="card">
        <p><strong>Игрок:</strong> <?= htmlspecialchars($playerName) ?></p>
        <p><strong>Дата:</strong> <?= htmlspecialchars($playedAt) ?></p>
        <p><strong>Выражение:</strong> <?= htmlspecialchars($expression) ?></p>
        <p><strong>Ваш ответ:</strong> <?= htmlspecialchars($userAnswer) ?></p>
        <p><strong>Правильный ответ:</strong> <?= $correctAnswer ?></p>

        <?php if ($isCorrect === 1): ?>
            <p class="success"><strong>Ответ верный.</strong></p>
        <?php else: ?>
            <p class="error"><strong>Ответ неверный.</strong></p>
        <?php endif; ?>
    </div>

    <p class="links">
        <a href="/">Новая игра</a>
        <a href="/history.php">История игр</a>
    </p>
</div>
</body>
</html>