<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';

use function SakatoGin\Calculator\getGames;
use function SakatoGin\Calculator\initializeDatabase;

initializeDatabase();
$games = getGames();
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>История игр</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<div class="container">
    <h1>История игр</h1>

    <?php if ($games === []): ?>
        <div class="card">
            <p>История пока пуста.</p>
        </div>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>Игрок</th>
                <th>Дата</th>
                <th>Выражение</th>
                <th>Ответ игрока</th>
                <th>Правильный ответ</th>
                <th>Результат</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($games as $game): ?>
                <tr>
                    <td><?= htmlspecialchars($game['player_name']) ?></td>
                    <td><?= htmlspecialchars($game['played_at']) ?></td>
                    <td><?= htmlspecialchars($game['expression']) ?></td>
                    <td><?= htmlspecialchars($game['user_answer']) ?></td>
                    <td><?= htmlspecialchars((string) $game['correct_answer']) ?></td>
                    <td>
                        <?php if ((int) $game['is_correct'] === 1): ?>
                            <span class="success">Верно</span>
                        <?php else: ?>
                            <span class="error">Неверно</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p class="links">
        <a href="/">На главную</a>
    </p>
</div>
</body>
</html>