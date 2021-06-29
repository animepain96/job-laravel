<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Hamcrest\Core\Set;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    public static function get($key, $type = 'string')
    {
        $value = Setting::getValue($key)->value;
        if($value) {
            try {
                if(settype($value, $type)) {
                    return $value;
                }
                return null;
            } catch (\Exception $ex) {
                Log::error($ex->getMessage());
                return null;
            }
        }

        return null;
    }

    public static function set($key, $value)
    {
        $result = Setting::updateOrCreate([
            'key' => $key,
        ],[
            'value' => $value,
        ]);

        if($result) {
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

    public function updateUnpaidThreshold(Request $request)
    {
        $value = $request->get('unpaid_threshold');
        if(self::set('unpaid_threshold', $value))
        {
            return response()
                ->json(['status' => 'success', 'data' => $value]);
        }

        return response()
            ->json(['status' => 'error']);
    }
}
