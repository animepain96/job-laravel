<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SummaryController extends Controller
{
    public function chartReport(Request $request)
    {
        $now = Carbon::now();
        try {
            $from = $request->get('from') ? Carbon::createFromFormat('Y-m-d', $request->get('from')) : Carbon::create($now->year, 1, 1);
            $to = $request->get('to') ? Carbon::createFromFormat('Y-m-d', $request->get('to')) : $now;
        } catch (\Exception $ex) {
            $from = Carbon::create($now->year, 1, 1);
            $to = $now;
        }
        $labels = [];

        $label = $from->copy();

        while ($label < $to) {
            $labels[] = $label->format('Y-m');
            $label = $label->addMonth(1);
        }

        $saleRevenues = Job::whereBetween('StartDate', [$from, $to])
            ->selectRaw('sum("Price") as revenue, to_char("StartDate", \'YYYY-MM\') as month') // ->selectRaw('sum(Price) as revenue, date_format(StartDate, "%Y-%m") as month')
            ->groupBy(DB::raw('to_char("StartDate", \'YYYY-MM\')')) //->groupBy(DB::raw('date_format(StartDate, "%Y-%m")'))
            ->get();

        $paymentRevenues = Job::whereBetween('Paydate', [$from, $to])
            ->where('Paid', true)
            ->selectRaw('sum("Price") as revenue, to_char("Paydate", \'YYYY-MM\') as month') // ->selectRaw('sum(Price) as revenue, date_format(Paydate, "%Y-%m") as month')
            ->groupBy(DB::raw('to_char("Paydate", \'YYYY-MM\')')) //->groupBy(DB::raw('date_format(Paydate, "%Y-%m")'))
            ->get();

        $data = [];
        $data['months'] = $labels;

        foreach ($labels as $name) {
            if ($saleRevenues->contains('month', $name)) {
                $data['sales'][] = $saleRevenues->firstWhere('month', $name)->revenue;
            } else {
                $data['sales'][] = 0;
            }

            if ($paymentRevenues->contains('month', $name)) {
                $data['payments'][] = $paymentRevenues->firstWhere('month', $name)->revenue;
            } else {
                $data['payments'][] = 0;
            }
        }

        return response()
            ->json(['data' => $data, 'status' => 'success']);
    }

    public function unpaidCount()
    {
        $unpaidCustomers = Customer::leftJoin('Job', 'Job.CustomerID', '=', 'Customer.ID')
            ->where('Job.Paid', false)
            ->groupBy('Customer.ID')
            ->havingRaw('sum("Job"."Price") > ?', [SettingController::get('unpaid_threshold', 'int') ?? env('UNPAID_THRESHOLD')])
            ->select('Customer.ID')
            ->get();

        return response()
            ->json(['data' => $unpaidCustomers->count(), 'status' => 'success']);
    }

}
