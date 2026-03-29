<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('app:about-game', function (): void {
    $this->comment('Task04 calculator game on Laravel.');
})->purpose('Display basic information about the calculator game app.');
