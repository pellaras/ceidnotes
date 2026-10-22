<?php

use Illuminate\Console\Scheduling\Event;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Str;

it('schedules the backup clean, run and monitor commands in that order', function () {
    $commands = collect(Schedule::events())
        ->map(fn (Event $event) => Str::afterLast($event->command, ' '))
        ->all();

    expect($commands)->toBe(['backup:clean', 'backup:run', 'backup:monitor']);
});

it('runs the backup commands daily at 06:02 in production only', function (string $environment, string $now, bool $isDue) {
    $this->app['env'] = $environment;
    $this->travelTo($now);

    $due = collect(Schedule::events())->map->isDue($this->app)->all();

    expect($due)->toBe([$isDue, $isDue, $isDue]);
})->with([
    'production at 06:02' => ['production', '2026-10-22 06:02:00', true],
    'production at 06:02 the next day' => ['production', '2026-10-23 06:02:00', true],
    'production at 06:01' => ['production', '2026-10-22 06:01:00', false],
    'production at 06:03' => ['production', '2026-10-22 06:03:00', false],
    'local at 06:02' => ['local', '2026-10-22 06:02:00', false],
    'testing at 06:02' => ['testing', '2026-10-22 06:02:00', false],
]);
