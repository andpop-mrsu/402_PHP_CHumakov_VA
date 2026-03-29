# Calculator SPA

Лабораторная работа 3 по дисциплине «Технологии разработки серверных приложений на PHP».

## Описание

Приложение реализует игру `calculator` в виде Single Page Application.

Frontend работает в браузере и обменивается с сервером данными в JSON-формате через REST API.  
Backend построен на микрофреймворке Slim.  
Для хранения данных используется SQLite.

## Структура

- `public` — корень сайта
- `public/index.php` — единственная точка входа backend
- `src` — классы приложения
- `db` — база данных SQLite

## Установка зависимостей

```bash
composer install
```

Если проект создаётся с нуля:

```bash
composer require slim/slim:"4.*" slim/psr7
composer dump-autoload -o
```

## Инициализация базы данных

```bash
php init_db.php
```

## Запуск

Из каталога `Task03` выполнить:

```bash
php -S localhost:3000 -t public
```

После этого открыть в браузере:

```text
http://localhost:3000/
```

## REST API

- `GET /games` — список всех игр
- `GET /games/{id}` — одна игра и её ходы
- `POST /games` — создать новую игру
- `POST /step/{id}` — добавить ход в игру

## Автор

**Чумаков В.А.**  
GitHub: **SakatoGin**