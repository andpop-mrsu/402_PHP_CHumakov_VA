<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Calculator Laravel</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            color: #111827;
        }

        .container {
            max-width: 960px;
            margin: 32px auto;
            padding: 0 16px;
        }

        .card {
            background: #ffffff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .hidden {
            display: none;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            margin-bottom: 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
        }

        button {
            background: #2563eb;
            color: #ffffff;
            border: none;
            padding: 10px 14px;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .game-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #eef2ff;
        }

        .error {
            color: #b91c1c;
            margin-top: 8px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Calculator на Laravel</h1>
    <p>Лабораторная работа 4. Игра с SQLite и историей ходов.</p>

    <section class="card">
        <h2>Новая игра</h2>
        <form id="start-form">
            <label for="player_name">Имя игрока</label>
            <input type="text" id="player_name" name="player_name" required>
            <button type="submit">Начать игру</button>
            <p class="error hidden" id="start-error"></p>
        </form>
    </section>

    <section class="card hidden" id="current-game-card">
        <h2>Текущая игра</h2>
        <p><strong>ID игры:</strong> <span id="game-id"></span></p>
        <p><strong>Игрок:</strong> <span id="game-player"></span></p>
        <p><strong>Выражение:</strong> <span id="game-expression"></span></p>

        <form id="step-form">
            <label for="user_answer">Ваш ответ</label>
            <input type="number" id="user_answer" name="user_answer" required>
            <button type="submit">Отправить ответ</button>
            <p class="error hidden" id="step-error"></p>
        </form>
    </section>

    <section class="card hidden" id="result-card">
        <h2>Результат</h2>
        <div id="result-box"></div>
    </section>

    <section class="card">
        <h2>Все игры</h2>
        <button id="reload-games" type="button">Обновить список</button>
        <div id="games-list"></div>
    </section>

    <section class="card">
        <h2>Детали игры</h2>
        <div id="game-details">Выберите игру из списка.</div>
    </section>
</div>

<script>
    let currentGameId = null;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const startForm = document.getElementById('start-form');
    const stepForm = document.getElementById('step-form');
    const reloadGamesButton = document.getElementById('reload-games');

    const startError = document.getElementById('start-error');
    const stepError = document.getElementById('step-error');

    const currentGameCard = document.getElementById('current-game-card');
    const resultCard = document.getElementById('result-card');

    const gameIdEl = document.getElementById('game-id');
    const gamePlayerEl = document.getElementById('game-player');
    const gameExpressionEl = document.getElementById('game-expression');
    const resultBoxEl = document.getElementById('result-box');
    const gamesListEl = document.getElementById('games-list');
    const gameDetailsEl = document.getElementById('game-details');

    async function request(url, options = {}) {
        const config = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                ...(options.headers || {})
            },
            ...options
        };

        const response = await fetch(url, config);
        const contentType = response.headers.get('Content-Type') || '';
        const isJson = contentType.includes('application/json');
        const data = isJson ? await response.json() : await response.text();

        if (!response.ok) {
            const message = isJson && data.message
                ? data.message
                : (isJson && data.error ? data.error : 'Request failed');
            throw new Error(message);
        }

        return data;
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function setError(element, message) {
        element.textContent = message;
        element.classList.remove('hidden');
    }

    function clearError(element) {
        element.textContent = '';
        element.classList.add('hidden');
    }

    function showCurrentGame(game) {
        currentGameId = game.id;
        gameIdEl.textContent = game.id;
        gamePlayerEl.textContent = game.player_name;
        gameExpressionEl.textContent = game.expression;
        currentGameCard.classList.remove('hidden');
        resultCard.classList.add('hidden');
        stepForm.reset();
    }

    function showResult(step) {
        const status = Number(step.is_correct) === 1 ? 'Верно' : 'Неверно';

        resultBoxEl.innerHTML = `
            <p><strong>Ответ игрока:</strong> ${escapeHtml(step.user_answer)}</p>
            <p><strong>Правильный ответ:</strong> ${escapeHtml(step.correct_answer)}</p>
            <p><strong>Результат:</strong> ${escapeHtml(status)}</p>
            <p><strong>Дата хода:</strong> ${escapeHtml(step.created_at)}</p>
        `;

        resultCard.classList.remove('hidden');
    }

    async function loadGames() {
        const games = await request('/api/games');

        if (games.length === 0) {
            gamesListEl.innerHTML = '<p>Игр пока нет.</p>';
            return;
        }

        gamesListEl.innerHTML = games.map((game) => `
            <div class="game-item">
                <div>
                    <strong>#${escapeHtml(game.id)}</strong>
                    — ${escapeHtml(game.player_name)}
                    — ${escapeHtml(game.expression)}
                    — ходов: ${escapeHtml(game.steps_count)}
                </div>
                <button type="button" data-game-id="${escapeHtml(game.id)}">Открыть</button>
            </div>
        `).join('');

        document.querySelectorAll('[data-game-id]').forEach((button) => {
            button.addEventListener('click', async () => {
                await loadGameDetails(button.dataset.gameId);
            });
        });
    }

    async function loadGameDetails(gameId) {
        const data = await request(`/api/games/${gameId}`);
        const game = data.game;
        const steps = data.steps;

        const stepsHtml = steps.length === 0
            ? '<p>Ходов пока нет.</p>'
            : `
                <table>
                    <thead>
                        <tr>
                            <th>ID хода</th>
                            <th>Ответ игрока</th>
                            <th>Правильный ответ</th>
                            <th>Результат</th>
                            <th>Дата</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${steps.map((step) => `
                            <tr>
                                <td>${escapeHtml(step.id)}</td>
                                <td>${escapeHtml(step.user_answer)}</td>
                                <td>${escapeHtml(step.correct_answer)}</td>
                                <td>${Number(step.is_correct) === 1 ? 'Верно' : 'Неверно'}</td>
                                <td>${escapeHtml(step.created_at)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;

        gameDetailsEl.innerHTML = `
            <p><strong>ID игры:</strong> ${escapeHtml(game.id)}</p>
            <p><strong>Игрок:</strong> ${escapeHtml(game.player_name)}</p>
            <p><strong>Дата начала:</strong> ${escapeHtml(game.started_at)}</p>
            <p><strong>Выражение:</strong> ${escapeHtml(game.expression)}</p>
            ${stepsHtml}
        `;
    }

    startForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearError(startError);

        try {
            const formData = new FormData(startForm);
            const playerName = formData.get('player_name');

            const game = await request('/api/games', {
                method: 'POST',
                body: JSON.stringify({
                    player_name: playerName
                })
            });

            showCurrentGame(game);
            await loadGames();
            await loadGameDetails(game.id);
        } catch (error) {
            setError(startError, error.message);
        }
    });

    stepForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearError(stepError);

        if (currentGameId === null) {
            setError(stepError, 'Сначала начните игру.');
            return;
        }

        try {
            const formData = new FormData(stepForm);
            const userAnswer = formData.get('user_answer');

            const step = await request(`/api/step/${currentGameId}`, {
                method: 'POST',
                body: JSON.stringify({
                    user_answer: userAnswer
                })
            });

            showResult(step);
            await loadGames();
            await loadGameDetails(currentGameId);
        } catch (error) {
            setError(stepError, error.message);
        }
    });

    reloadGamesButton.addEventListener('click', async () => {
        try {
            await loadGames();
        } catch (error) {
            gamesListEl.innerHTML = `<p>${escapeHtml(error.message)}</p>`;
        }
    });

    document.addEventListener('DOMContentLoaded', async () => {
        try {
            await loadGames();
        } catch (error) {
            gamesListEl.innerHTML = `<p>${escapeHtml(error.message)}</p>`;
        }
    });
</script>
</body>
</html>
