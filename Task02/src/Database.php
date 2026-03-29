<?php

declare(strict_types=1);

namespace SakatoGin\Calculator;

use PDO;

require_once __DIR__ . '/bootstrap.php';

function getConnection(): PDO
{
    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $pdo;
}

function initializeDatabase(): void
{
    $pdo = getConnection();

    $sql = <<<SQL
    CREATE TABLE IF NOT EXISTS games (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        player_name TEXT NOT NULL,
        played_at TEXT NOT NULL,
        expression TEXT NOT NULL,
        correct_answer INTEGER NOT NULL,
        user_answer TEXT NOT NULL,
        is_correct INTEGER NOT NULL
    )
    SQL;

    $pdo->exec($sql);
}

function saveGame(
    string $playerName,
    string $playedAt,
    string $expression,
    int $correctAnswer,
    string $userAnswer,
    int $isCorrect
): void {
    $pdo = getConnection();

    $stmt = $pdo->prepare(
        'INSERT INTO games (player_name, played_at, expression, correct_answer, user_answer, is_correct)
         VALUES (:player_name, :played_at, :expression, :correct_answer, :user_answer, :is_correct)'
    );

    $stmt->execute([
        ':player_name' => $playerName,
        ':played_at' => $playedAt,
        ':expression' => $expression,
        ':correct_answer' => $correctAnswer,
        ':user_answer' => $userAnswer,
        ':is_correct' => $isCorrect,
    ]);
}

function getGames(): array
{
    $pdo = getConnection();

    $stmt = $pdo->query(
        'SELECT player_name, played_at, expression, correct_answer, user_answer, is_correct
         FROM games
         ORDER BY id DESC'
    );

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}