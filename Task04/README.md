# Calculator Laravel

Лабораторная работа 4 по дисциплине «Технологии разработки серверных приложений на PHP».

## Описание

Приложение реализует игру `calculator` на фреймворке Laravel с использованием SQLite.

Возможности:
- запуск новой игры;
- генерация случайного арифметического выражения;
- отправка ответа игрока;
- сохранение игр и ходов в базе данных SQLite;
- просмотр истории игр и ходов.

## Структура

- `app` — контроллеры, модели и сервисы приложения
- `database` — база данных SQLite и миграции
- `public` — точка входа приложения
- `resources/views` — Blade-шаблон страницы
- `routes` — маршруты приложения

## Установка

### Linux / WSL

В корне проекта выполнить:

```bash
make install
```

Команда автоматически:
- установит зависимости Composer;
- создаст файл `.env`, если его ещё нет;
- создаст файл базы данных SQLite;
- сгенерирует ключ приложения;
- выполнит миграции.

### Windows PowerShell

Если `make` недоступен, выполните по шагам:

```powershell
composer install
Copy-Item .env.example .env
New-Item database/database.sqlite -ItemType File -Force
php artisan key:generate --force
php artisan migrate --force
```

## Запуск

Из каталога `Task04` выполнить:

```bash
php artisan serve
```

После этого открыть в браузере:

```text
http://localhost:8000/
```

## API

Внутри приложения используются маршруты:

- `GET /api/games`
- `GET /api/games/{id}`
- `POST /api/games`
- `POST /api/step/{id}`
