<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Services\ContentModerationService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('moderate {text}', function (ContentModerationService $moderator) {
    $result = $moderator->moderateContent($this->argument('text'));

    if ($result) {
        $this->info("Original text:\n" . $this->argument('text'));
        $this->info("\nModerated text:\n" . $result);
    } else {
        $this->error('Moderation failed');
    }
})->purpose('Test content moderation with provided text');

Artisan::command('preflight {text}', function (ContentModerationService $moderator) {
    $result = $moderator->preflight($this->argument('text'));

    if ($result) {
        $this->info("Original text:\n" . $this->argument('text'));
        $this->info("\nPreflight text:\n" . $result);
    } else {
        $this->error('Preflight failed');
    }
})->purpose('Test content preflight with provided text');
