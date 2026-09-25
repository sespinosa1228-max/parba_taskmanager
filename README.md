# Orbital Task Manager

A small Laravel task manager styled as a violet-toned astronaut mission console. Tasks support notes, optional target dates, pending/completed status, filtering, editing, and deletion.

## Project Details

- **Project Code:** WST21-PM-2026-SF
- **Student Name:** Polaris Cylurks J. Parba
- **Course & Year:** BSIT-2
- **Database Used:** SQLite

## Purpose

Orbital gives an individual one place to organize personal tasks, keep daily priorities visible, and track each task from pending to completed.

## Features

- Add tasks
- View and filter tasks
- Edit tasks
- Delete tasks
- Update task status

## Run locally

Requirements: PHP 8.3+, Composer, Node.js, and npm.

```sh
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Open the URL printed by `php artisan serve`. Demo missions are added by `--seed`; omit it for an empty task list.

## Tests

```sh
php artisan test --compact
```