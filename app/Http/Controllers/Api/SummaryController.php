<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    public function chartReport()
    {
        $now = Carbon::now();
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $saleRevenues = Job::whereYear('start_date', $now->year)
            ->selectRaw('sum(price) as revenue, month(start_date) as month')
            ->groupByRaw('month(start_date)')
            ->get();

        $data = [];

        foreach ($months as $index => $name) {
            if($saleRevenues->contains('month', $index + 1)) {
                array_push($data, [
                    'month' => $name,
                    'revenue' => $saleRevenues->firstWhere('month', $index + 1)->revenue,
                ]);
            } else {
                array_push($data, [
                    'month' => $name,
                    'revenue' => 0,
                ]);
            }
        }

        return response()
            ->json(['data' => $data, 'status' => 'success']);
    }
}
