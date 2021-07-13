<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();
        $from = $request->has('from') ? Carbon::createFromFormat('Y-m-d', $request->get('from')) : Carbon::create($now->year, $now->month, 1);
        $to = $request->has('to') ? Carbon::createFromFormat('Y-m-d', $request->get('to')) : Carbon::create($now->year, $now->month + 1, 1)->addDays(-1);
        $customer = $request->get('customer');
        $method = $request->get('method');
        $type = $request->get('type');
        $mode = $request->get('mode');
        $paid = $request->get('paid');

        $jobs = Job::with([
            'customer' => function ($query) {
                $query->withTrashed();
            }, 'type' => function ($query) {
                $query->withTrashed();
            }, 'method' => function ($query) {
                $query->withTrashed();
            }
        ])
            ->when($from && $to, function ($query) use ($from, $to, $mode) {
                if ($mode == '1') {
                    $query->whereBetween('Paydate', [$from, $to]);
                } else {
                    $query->whereBetween('StartDate', [$from, $to]);
                }
            })
            ->when($customer && $customer != '0', function ($query) use ($customer) {
                $query->where('CustomerID', $customer);
            })
            ->when($method && $method != '0', function ($query) use ($method) {
                $query->where('MethodID', $method);
            })
            ->when($type && $type != '0', function ($query) use ($type) {
                $query->where('TypeID', $type);
            })
            ->when($paid, function ($query) use ($paid) {
                if ($paid == '1') {
                    $query->where('Paid', true);
                }
                if ($paid == '2') {
                    $query->where('Paid', false);
                }
            })
            ->get();
        return response()
            ->json(['data' => $jobs, 'status' => 'success']);
    }
}
