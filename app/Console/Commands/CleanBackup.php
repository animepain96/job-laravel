<?php

namespace App\Console\Commands;

use App\Events\Backups\CleanFail;
use App\Events\Backups\CleanSuccess;
use App\Http\Controllers\Api\SettingController;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CleanBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old backup than setting keep days value';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $keepDays = SettingController::get('keep_days', 'int');
            $backupPath = env('BACKUP_FOLDER', 'backups');

            $allFiles = Storage::allFiles($backupPath);
            $cleanFiles = [];

            foreach ($allFiles as $file) {
                if(Str::contains($file, 'auto')) {
                    $date = Carbon::createFromTimestamp(Storage::lastModified($file))->format('Y-m-d H:i:s');
                    if (Carbon::now()->diffInDays($date) > $keepDays) {
                        array_push($cleanFiles, $file);
                    }
                }
            }

            if (!empty($cleanFiles)) {
                Storage::delete($cleanFiles);
                event(new CleanSuccess($cleanFiles));
            }
        } catch (\Exception $ex) {
            event(new CleanFail($ex->getMessage()));
        }
    }
}
