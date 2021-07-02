<?php

namespace App\Listeners\Backup;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BackupListener
{

    public function onBackupSuccess($events)
    {

    }

    public function onManifestCreated($events)
    {

    }

    public function onZipCreated($events)
    {

    }

    public function onBackupFailed(...$events)
    {

    }

    public function subscribe($events) {
        $events->listen('Spatie\Backup\Events\BackupWasSuccessful', 'App\Listeners\Backup\BackupListener@onBackupSuccess');
        $events->listen('Spatie\Backup\Events\BackupManifestWasCreated', 'App\Listeners\Backup\BackupListener@onManifestCreated');
        $events->listen('Spatie\Backup\Events\BackupZipWasCreated', 'App\Listeners\Backup\BackupListener@onZipCreated');
        $events->listen('Spatie\Backup\Events\BackupHasFailed', 'App\Listeners\Backup\BackupListener@onBackupFailed');
    }
}
