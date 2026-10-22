<?php

use Spatie\Backup\Config\Config;
use Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification;
use Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification;
use Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification;
use Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification;
use Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification;
use Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification;

it('boots with the placeholder recipient when BACKUP_NOTIFICATION_EMAIL is unset', function () {
    $config = app(Config::class);

    expect($config->notifications->mail->to)->toBe('your@example.com');
});

it('backs up to the azure and local disks', function () {
    $config = app(Config::class);

    expect($config->backup->destination->disks)->toBe(['azure', 'local']);
});

it('mails only when a backup or cleanup fails or a backup is unhealthy', function () {
    $config = app(Config::class);

    expect($config->notifications->notifications)->toBe([
        BackupHasFailedNotification::class => ['mail'],
        UnhealthyBackupWasFoundNotification::class => ['mail'],
        CleanupHasFailedNotification::class => ['mail'],
        BackupWasSuccessfulNotification::class => [],
        HealthyBackupWasFoundNotification::class => [],
        CleanupWasSuccessfulNotification::class => [],
    ]);
});
