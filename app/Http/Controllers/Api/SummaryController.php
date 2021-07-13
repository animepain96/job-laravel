<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;

class SummaryController extends Controller
{
    public function chartReport()
    {
        try{
            $now = Carbon::now();
            $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            $saleRevenues = Job::whereYear('StartDate', $now->year)
                ->selectRaw('sum("Price") as revenue, extract(month from "StartDate") as month') // month(StartDate) //extract(month from StartDate)
                ->groupBy(DB::raw('extract(month from "StartDate")')) //month(StartDate) //extract(month from StartDate)
                ->get();

            $data = [];

            foreach ($months as $index => $name) {
                if ($saleRevenues->contains('month', $index + 1)) {
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
        catch (\Exception $ex){
            dd($ex);
        }
    }

    public function unpaidCount()
    {
        try {
            $unpaidCustomers = Customer::leftJoin('Job', 'Job.CustomerID', '=', 'Customer.ID')
                ->where('Job.Paid', false)
                ->groupBy('Customer.ID')
                ->havingRaw('sum("Job"."Price") > ?', [SettingController::get('unpaid_threshold', 'int') ?? env('UNPAID_THRESHOLD')])
                ->select('Customer.ID')
                ->get();

            return response()
                ->json(['data' => $unpaidCustomers->count(), 'status' => 'success']);
        } catch (\Exception $ex) {
            dd($ex);
        }
    }

}
