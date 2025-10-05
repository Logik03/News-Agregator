<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\IngestArticlesJob;
use Illuminate\Console\Scheduling\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Artisan::command('fetch:newsapi', function () {
    IngestArticlesJob::dispatch('newsapi');
    $this->info('Dispatched newsapi job.');
})->describe('Fetch NewsAPI articles');

Artisan::command('fetch:guardian', function () {
    IngestArticlesJob::dispatch('guardian');
    $this->info('Dispatched guardian job.');
})->describe('Fetch Guardian articles');

Artisan::command('fetch:nyt', function () {
    IngestArticlesJob::dispatch('nyt');
    $this->info('Dispatched NYT job.');
})->describe('Fetch NYT articles');


app()->booted(function () {
    $schedule = app(Schedule::class);

    $schedule->command('fetch:newsapi')
        ->everyFiveMinutes()
        ->withoutOverlapping()
        ->runInBackground();

    $schedule->command('fetch:guardian')
        ->everyFiveMinutes()
        ->withoutOverlapping()
        ->runInBackground();

    $schedule->command('fetch:nyt')
        ->everyFiveMinutes()
        ->withoutOverlapping()
        ->runInBackground();
});