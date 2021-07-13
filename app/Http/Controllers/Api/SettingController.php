<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\IntValueRequest;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    public static function get($key, $type = 'string')
    {
        try {
            $value = Setting::getValue($key)->value;
            if ($value) {
                if (settype($value, $type)) {
                    return $value;
                }
            }

            return null;
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return null;
        }
    }

    public static function set($key, $value)
    {
        $result = Setting::updateOrCreate([
            'key' => $key,
        ], [
            'value' => $value,
        ]);

        if ($result) {
            return true;
        }

        return false;
    }

    public function unpaidThreshold()
    {
        $unpaidThreshold = self::get('unpaid_threshold', 'int') ?? env('UNPAID_THRESHOLD');
        return response()
            ->json(['data' => $unpaidThreshold, 'status' => 'success']);
    }

    public function updateUnpaidThreshold(IntValueRequest $request)
    {
        $value = $request->get('value');
        if (self::set('unpaid_threshold', $value)) {
            return response()
                ->json(['status' => 'success', 'data' => $value]);
        }

        return response()
            ->json(['status' => 'error']);
    }

    public function keepDays()
    {
        $keepDays = self::get('keep_days', 'int') ?? env('BACKUP_KEEP_DAYS');
        return response()
            ->json(['data' => $keepDays, 'status' => 'success']);
    }

    public function updateKeepDays(IntValueRequest $request)
    {
        $value = $request->get('value');
        if (self::set('keep_days', $value)) {
            return response()
                ->json(['status' => 'success', 'data' => $value]);
        }

        return response()
            ->json(['status' => 'error']);
    }
}
