<?php

use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GameController::class, 'page']);

Route::prefix('api')->group(function (): void {
    Route::get('/games', [GameController::class, 'games']);
    Route::get('/games/{id}', [GameController::class, 'show'])->whereNumber('id');
    Route::post('/games', [GameController::class, 'createGame']);
    Route::post('/step/{id}', [GameController::class, 'createStep'])->whereNumber('id');
});
