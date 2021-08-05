<?php

namespace App\Listeners\Backup;

use App\Jobs\Backups\SendMailFailBackup;
use App\Jobs\Backups\SendMailFailClean;
use App\Jobs\Backups\SendMailSuccessBackup;
use App\Jobs\Backups\SendMailSuccessClean;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BackupListener
{

    public function onBackupSuccess($events)
    {
        $data['name'] = basename(storage_path($events->backupDestination->newestBackup()->path()));
        $data['size'] = round(Storage::size($events->backupDestination->newestBackup()->path()) / 1024, 2) . 'KB';
        $mailJob = new SendMailSuccessBackup($data);
        dispatch($mailJob);
    }

    public function onBackupFailed($events)
    {
        \Log::info($events->exception->getMessage());
        $data['name'] = basename(storage_path($events->backupDestination->newestBackup()->path()));
        $data['exception'] = $events->exception->getMessage();
        $mailJob = new SendMailFailBackup($data);
        dispatch($mailJob);
    }

    public function onCleanSuccess($events)
    {
        $data['fileList'] = $events->fileList;
        $mailJob = new SendMailSuccessClean($data);
        dispatch($mailJob);
    }

    public function onCleanFail($events)
    {
        $data['exception'] = $events->exception->getMessage();
        $mailJob = new SendMailFailClean($data);
        dispatch($mailJob);
    }

    public function subscribe($events) {
        $events->listen('Spatie\Backup\Events\BackupWasSuccessful', 'App\Listeners\Backup\BackupListener@onBackupSuccess');
        $events->listen('Spatie\Backup\Events\BackupHasFailed', 'App\Listeners\Backup\BackupListener@onBackupFailed');
        $events->listen('App\Events\Backups\CleanSuccess', 'App\Listeners\Backup\BackupListener@onCleanSuccess');
        $events->listen('App\Events\Backups\CleanFail', 'App\Listeners\Backup\BackupListener@onCleanFail');
    }
}
