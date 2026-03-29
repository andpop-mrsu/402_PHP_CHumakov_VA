<?php

namespace App\Services;

use InvalidArgumentException;

class GameService
{
    public static function generateRound(): array
    {
        $operands = [
            random_int(1, 50),
            random_int(1, 50),
            random_int(1, 50),
            random_int(1, 50),
        ];

        $operators = [
            self::randomOperator(),
            self::randomOperator(),
            self::randomOperator(),
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

        return [
            'expression' => $expression,
            'correct_answer' => self::evaluateExpression($operands, $operators),
        ];
    }

    public static function evaluateExpressionFromString(string $expression): int
    {
        $tokens = preg_split('/\s+/', trim($expression));

        if ($tokens === false || count($tokens) !== 7) {
            throw new InvalidArgumentException('Invalid expression.');
        }

        $operands = [
            (int) $tokens[0],
            (int) $tokens[2],
            (int) $tokens[4],
            (int) $tokens[6],
        ];

        $operators = [
            $tokens[1],
            $tokens[3],
            $tokens[5],
        ];

        return self::evaluateExpression($operands, $operators);
    }

    private static function randomOperator(): string
    {
        $operators = ['+', '-', '*'];

        return $operators[array_rand($operators)];
    }

    private static function evaluateExpression(array $operands, array $operators): int
    {
        $numbers = $operands;
        $ops = $operators;

        for ($i = 0; $i < count($ops);) {
            if ($ops[$i] === '*') {
                $numbers[$i] *= $numbers[$i + 1];
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
            } else {
                $result -= $numbers[$i + 1];
            }
        }

        return $result;
    }
}
