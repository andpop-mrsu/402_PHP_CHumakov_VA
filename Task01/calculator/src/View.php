<?php

declare(strict_types=1);

namespace SakatoGin\Calculator\View;

use function cli\line;
use function cli\prompt;

function showWelcome(): void
{
    line('=== Calculator Game ===');
    line('Вычислите арифметическое выражение.');
    line('');
}

function askPlayerName(): string
{
    $name = prompt('Введите ваше имя');

    return trim($name);
}

function greetPlayer(string $name): void
{
    line("Привет, %s!", $name);
}

function showExpression(string $expression): void
{
    line('');
    line('Выражение: %s', $expression);
}

function askAnswer(): string
{
    return trim(prompt('Ваш ответ'));
}

function showCorrectMessage(string $name): void
{
    line('Верно, %s!', $name);
}

function showWrongMessage(string $name, string $correctAnswer): void
{
    line('Неверно, %s.', $name);
    line('Правильный ответ: %s', $correctAnswer);
}

function showGoodbye(): void
{
    line('');
    line('Игра окончена.');
}
