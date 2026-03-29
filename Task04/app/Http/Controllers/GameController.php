<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Step;
use App\Services\GameService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GameController extends Controller
{
    public function page(): View
    {
        return view('app');
    }

    public function games(): JsonResponse
    {
        $games = Game::query()
            ->withCount('steps')
            ->orderByDesc('id')
            ->get();

        return response()->json($games);
    }

    public function show(int $id): JsonResponse
    {
        $game = Game::query()->find($id);

        if ($game === null) {
            return response()->json(['error' => 'Game not found'], 404);
        }

        $steps = Step::query()
            ->where('game_id', $id)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'game' => $game,
            'steps' => $steps,
        ]);
    }

    public function createGame(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'player_name' => ['required', 'string', 'max:100'],
        ]);

        $round = GameService::generateRound();

        $game = Game::query()->create([
            'player_name' => $validated['player_name'],
            'started_at' => now()->format('Y-m-d H:i:s'),
            'expression' => $round['expression'],
            'correct_answer' => $round['correct_answer'],
        ]);

        return response()->json($game, 201);
    }

    public function createStep(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'user_answer' => ['required'],
        ]);

        $game = Game::query()->find($id);

        if ($game === null) {
            return response()->json(['error' => 'Game not found'], 404);
        }

        $userAnswer = trim((string) $validated['user_answer']);
        $correctAnswer = (int) $game->correct_answer;
        $isCorrect = ((string) $correctAnswer === $userAnswer) ? 1 : 0;

        $step = Step::query()->create([
            'game_id' => $game->id,
            'user_answer' => $userAnswer,
            'correct_answer' => $correctAnswer,
            'is_correct' => $isCorrect,
            'created_at' => now()->format('Y-m-d H:i:s'),
        ]);

        return response()->json($step, 201);
    }
}
