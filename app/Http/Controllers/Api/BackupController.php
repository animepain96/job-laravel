<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backup\BackupRequest;
use App\Http\Requests\Backup\DownloadBackupRequest;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackupController extends Controller
{
    public function manual()
    {
        try {
            $command = 'backup:run';
            $fileName = 'manual_' . Carbon::now()->format('Y-m-d-H-i-s') . '.zip';
            $params = [
                '--filename' => $fileName,
                '--only-db' => true,
            ];

            $result = Artisan::call($command, $params);
            if ($result == '0') {
                return response()
                    ->json(['status' => 'success']);
            }
            return response()
                ->json(['status' => 'error'], 500);
        } catch (\Exception $ex) {
            return response()
                ->json(['status' => 'error'], 500);
        }
    }

    public function index()
    {
        try {
            $backupFiles = Storage::allFiles('backups');

            $backups = [];

            if (!empty($backupFiles)) {
                foreach ($backupFiles as $file) {
                    $fileMeta = [];
                    $fileMeta['name'] = basename(storage_path($file));
                    $fileMeta['size'] = round(Storage::size($file) / 1024, 2);
                    $fileMeta['type'] = Str::contains($fileMeta['name'], 'manual') ? 'manual' : 'auto';
                    $fileMeta['modified'] = Carbon::createFromTimestamp(Storage::lastModified($file))->format('Y-m-d H:i:s');
                    array_push($backups, $fileMeta);
                }
            }

            return response()
                ->json(['data' => $backups, 'status' => 'success']);
        } catch (\Exception $ex) {
            return response()
                ->json(['status' => 'error'], 500);
        }
    }

    public function deleteBackup(BackupRequest $request)
    {
        $backups = $request->get('name');

        if(!empty($backups)) {

            foreach ($backups as $index => $name) {
                $backups[$index] = 'backups/'.$name;
            }

            $result = Storage::delete($backups);
            if($result) {
                return response()
                    ->json(['status' => 'success']);
            }

            return response()
                ->json(['status' => 'error']);
        }

        return response()
            ->json(['status' => 'error']);
    }

    public function download(DownloadBackupRequest $request)
    {
        $name = $request->get('name');
        $path = 'backups/'.$name;
        if(Storage::exists($path)) {
            return response()
                ->file(Storage::path($path));
        }

        return response()
            ->json(['status' => 'error']);
    }
}
