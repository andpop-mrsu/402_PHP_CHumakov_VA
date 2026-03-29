<?php

declare(strict_types=1);

namespace SakatoGin\Task03;

use PDO;

final class Database
{
    private const DB_PATH = __DIR__ . '/../db/calculator.sqlite';

    public static function initialize(): void
    {
        $pdo = self::connect();

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS games (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                player_name TEXT NOT NULL,
                started_at TEXT NOT NULL,
                expression TEXT NOT NULL,
                correct_answer INTEGER NOT NULL
            )'
        );

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS steps (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                game_id INTEGER NOT NULL,
                user_answer TEXT NOT NULL,
                correct_answer INTEGER NOT NULL,
                is_correct INTEGER NOT NULL,
                created_at TEXT NOT NULL,
                FOREIGN KEY (game_id) REFERENCES games(id)
            )'
        );
    }

    public static function createGame(string $playerName): array
    {
        $round = GameService::generateRound();
        $startedAt = date('Y-m-d H:i:s');

        $pdo = self::connect();
        $stmt = $pdo->prepare(
            'INSERT INTO games (player_name, started_at, expression, correct_answer)
             VALUES (:player_name, :started_at, :expression, :correct_answer)'
        );

        $stmt->execute([
            ':player_name' => $playerName,
            ':started_at' => $startedAt,
            ':expression' => $round['expression'],
            ':correct_answer' => $round['correct_answer'],
        ]);

        return [
            'id' => (int) $pdo->lastInsertId(),
            'player_name' => $playerName,
            'started_at' => $startedAt,
            'expression' => $round['expression'],
            'correct_answer' => $round['correct_answer'],
        ];
    }

    public static function addStep(int $gameId, string $userAnswer): ?array
    {
        $game = self::getGameRow($gameId);

        if ($game === null) {
            return null;
        }

        $createdAt = date('Y-m-d H:i:s');
        $correctAnswer = (int) $game['correct_answer'];
        $isCorrect = ((string) $correctAnswer === trim($userAnswer)) ? 1 : 0;

        $pdo = self::connect();
        $stmt = $pdo->prepare(
            'INSERT INTO steps (game_id, user_answer, correct_answer, is_correct, created_at)
             VALUES (:game_id, :user_answer, :correct_answer, :is_correct, :created_at)'
        );

        $stmt->execute([
            ':game_id' => $gameId,
            ':user_answer' => $userAnswer,
            ':correct_answer' => $correctAnswer,
            ':is_correct' => $isCorrect,
            ':created_at' => $createdAt,
        ]);

        return [
            'id' => (int) $pdo->lastInsertId(),
            'game_id' => $gameId,
            'user_answer' => $userAnswer,
            'correct_answer' => $correctAnswer,
            'is_correct' => $isCorrect,
            'created_at' => $createdAt,
        ];
    }

    public static function getGames(): array
    {
        $pdo = self::connect();

        $stmt = $pdo->query(
            'SELECT
                g.id,
                g.player_name,
                g.started_at,
                g.expression,
                COUNT(s.id) AS steps_count
             FROM games g
             LEFT JOIN steps s ON s.game_id = g.id
             GROUP BY g.id, g.player_name, g.started_at, g.expression
             ORDER BY g.id DESC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getGameWithSteps(int $gameId): ?array
    {
        $game = self::getGameRow($gameId);

        if ($game === null) {
            return null;
        }

        $pdo = self::connect();
        $stmt = $pdo->prepare(
            'SELECT id, game_id, user_answer, correct_answer, is_correct, created_at
             FROM steps
             WHERE game_id = :game_id
             ORDER BY id DESC'
        );
        $stmt->execute([':game_id' => $gameId]);

        return [
            'game' => $game,
            'steps' => $stmt->fetchAll(PDO::FETCH_ASSOC),
        ];
    }

    private static function getGameRow(int $gameId): ?array
    {
        $pdo = self::connect();
        $stmt = $pdo->prepare(
            'SELECT id, player_name, started_at, expression, correct_answer
             FROM games
             WHERE id = :id'
        );
        $stmt->execute([':id' => $gameId]);

        $game = $stmt->fetch(PDO::FETCH_ASSOC);

        return $game === false ? null : $game;
    }

    private static function connect(): PDO
    {
        $pdo = new PDO('sqlite:' . self::DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }
}
