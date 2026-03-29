<?php

declare(strict_types=1);

namespace SakatoGin\Calculator\Controller;

use function SakatoGin\Calculator\View\askAnswer;
use function SakatoGin\Calculator\View\askPlayerName;
use function SakatoGin\Calculator\View\greetPlayer;
use function SakatoGin\Calculator\View\showCorrectMessage;
use function SakatoGin\Calculator\View\showExpression;
use function SakatoGin\Calculator\View\showGoodbye;
use function SakatoGin\Calculator\View\showWelcome;
use function SakatoGin\Calculator\View\showWrongMessage;

function startGame(): void
{
    showWelcome();

    $name = askPlayerName();
    greetPlayer($name);

    [$expression, $correctAnswer] = generateRound();

    showExpression($expression);

    $userAnswer = askAnswer();

    if ($userAnswer === (string) $correctAnswer) {
        showCorrectMessage($name);
    } else {
        showWrongMessage($name, (string) $correctAnswer);
    }

    showGoodbye();
}

function generateRound(): array
{
    $operands = [
        random_int(1, 50),
        random_int(1, 50),
        random_int(1, 50),
        random_int(1, 50),
    ];

    $operators = [
        randomOperator(),
        randomOperator(),
        randomOperator(),
    ];

    $expression = sprintf(
        '%d %s %d %s %d %s %d',
        $operands[0],
        $operators[0],
        $operands[1],
        $operators[1],
        $operands[2],
        $operators[2],
        $operands[3]
    );

    $value = evaluateExpression($operands, $operators);

    return [$expression, $value];
}

function randomOperator(): string
{
    $operators = ['+', '-', '*'];
    $index = array_rand($operators);

    return $operators[$index];
}

function evaluateExpression(array $operands, array $operators): int
{
    $numbers = $operands;
    $ops = $operators;

    for ($i = 0; $i < count($ops);) {
        if ($ops[$i] === '*') {
            $numbers[$i] = $numbers[$i] * $numbers[$i + 1];
            array_splice($numbers, $i + 1, 1);
            array_splice($ops, $i, 1);
            continue;
        }

        $i++;
    }

    $result = $numbers[0];

    for ($i = 0; $i < count($ops); $i++) {
        if ($ops[$i] === '+') {
            $result += $numbers[$i + 1];
        } elseif ($ops[$i] === '-') {
            $result -= $numbers[$i + 1];
        }
    }

    return $result;
}
