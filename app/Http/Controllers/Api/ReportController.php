<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request) {
        $now = Carbon::now();
        $from = $request->has('from') ? Carbon::parse($request->get('from')) : Carbon::create($now->year, $now->month, 1);
        $to = $request->has('to') ? Carbon::parse($request->get('to')) : Carbon::create($now->year, $now->month + 1, 1)->addDays(-1);
        $customer = $request->get('customer');
        $method = $request->get('method');
        $type = $request->get('type');
        $mode = $request->get('mode');
        $paid = $request->get('paid');

        $jobs = Job::with('customer', 'type', 'method')
            ->when($from && $to, function ($query) use ($from, $to, $mode) {
                if($mode == '1') {
                    $query->whereBetween('pay_date', [$from, $to]);
                } else {
                    $query->whereBetween('start_date', [$from, $to]);
                }
            })
            ->when($customer && $customer != '0', function ($query) use ($customer) {
                $query->where('customer_id', $customer);
            })
            ->when($method && $method != '0', function ($query) use ($method) {
                $query->where('method_id', $method);
            })
            ->when($type && $type != '0', function ($query) use ($type) {
                $query->where('type_id', $type);
            })
            ->when($paid, function ($query) use ($paid) {
                if($paid == '1') {
                    $query->where('paid', true);
                }
                if($paid == '2') {
                    $query->where('paid', false);
                }
            })
            ->get();
        return response()
            ->json(['data' => $jobs, 'status' => 'success']);
    }
}
