<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SakatoGin\Task03\Database;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

Database::initialize();

$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

function jsonResponse(Response $response, mixed $data, int $status = 200): Response
{
    $response->getBody()->write(
        json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    );

    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withStatus($status);
}

$app->get('/', function (Request $request, Response $response): Response {
    $html = file_get_contents(__DIR__ . '/index.html');

    $response->getBody()->write($html);

    return $response->withHeader('Content-Type', 'text/html; charset=utf-8');
});

$app->get('/games', function (Request $request, Response $response): Response {
    return jsonResponse($response, Database::getGames());
});

$app->get('/games/{id:[0-9]+}', function (
    Request $request,
    Response $response,
    array $args
): Response {
    $gameId = (int) $args['id'];
    $data = Database::getGameWithSteps($gameId);

    if ($data === null) {
        return jsonResponse($response, ['error' => 'Game not found'], 404);
    }

    return jsonResponse($response, $data);
});

$app->post('/games', function (Request $request, Response $response): Response {
    $data = (array) ($request->getParsedBody() ?? []);
    $playerName = trim((string) ($data['player_name'] ?? ''));

    if ($playerName === '') {
        return jsonResponse($response, ['error' => 'player_name is required'], 400);
    }

    $game = Database::createGame($playerName);

    return jsonResponse($response, $game, 201);
});

$app->post('/step/{id:[0-9]+}', function (
    Request $request,
    Response $response,
    array $args
): Response {
    $gameId = (int) $args['id'];
    $data = (array) ($request->getParsedBody() ?? []);
    $userAnswer = trim((string) ($data['user_answer'] ?? ''));

    if ($userAnswer === '') {
        return jsonResponse($response, ['error' => 'user_answer is required'], 400);
    }

    $step = Database::addStep($gameId, $userAnswer);

    if ($step === null) {
        return jsonResponse($response, ['error' => 'Game not found'], 404);
    }

    return jsonResponse($response, $step, 201);
});

$app->run();
