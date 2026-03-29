let currentGameId = null;

const startForm = document.getElementById('start-form');
const stepForm = document.getElementById('step-form');
const reloadGamesButton = document.getElementById('reload-games');

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
            ...(options.headers || {})
        },
        ...options
    };

    const response = await fetch(url, config);
    const contentType = response.headers.get('Content-Type') || '';
    const isJson = contentType.includes('application/json');
    const data = isJson ? await response.json() : await response.text();

    if (!response.ok) {
        const message = isJson && data.error ? data.error : 'Request failed';
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
    const games = await request('/games');

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
    const data = await request(`/games/${gameId}`);
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

    try {
        const formData = new FormData(startForm);
        const playerName = formData.get('player_name');

        const game = await request('/games', {
            method: 'POST',
            body: JSON.stringify({
                player_name: playerName
            })
        });

        showCurrentGame(game);
        await loadGames();
        await loadGameDetails(game.id);
    } catch (error) {
        alert(error.message);
    }
});

stepForm.addEventListener('submit', async (event) => {
    event.preventDefault();

    if (currentGameId === null) {
        alert('Сначала начните игру.');
        return;
    }

    try {
        const formData = new FormData(stepForm);
        const userAnswer = formData.get('user_answer');

        const step = await request(`/step/${currentGameId}`, {
            method: 'POST',
            body: JSON.stringify({
                user_answer: userAnswer
            })
        });

        showResult(step);
        await loadGames();
        await loadGameDetails(currentGameId);
    } catch (error) {
        alert(error.message);
    }
});

reloadGamesButton.addEventListener('click', async () => {
    try {
        await loadGames();
    } catch (error) {
        alert(error.message);
    }
});

document.addEventListener('DOMContentLoaded', async () => {
    try {
        await loadGames();
    } catch (error) {
        gamesListEl.innerHTML = `<p>${escapeHtml(error.message)}</p>`;
    }
});
