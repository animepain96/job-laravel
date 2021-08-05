<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $customers = Customer::leftJoin('Job', 'Job.CustomerID', '=', 'Customer.ID')
            ->where('Job.Paid', false)
            ->groupBy(['Customer.ID', 'Customer.Name', 'Customer.Note'])
            ->select('Customer.ID', 'Customer.Name', 'Customer.Note')
            ->when($request->has('q'), function ($query) use ($request) {
                $query->where(function ($where) use ($request) {
                    $where->orWhere('Customer.Name', 'like', '%' . $request->get('q') . '%')
                        ->orWhere('Customer.Note', 'like', '%' . $request->get('q') . '%');
                });
            })
            ->when($request->has('order'), function ($query) use ($request) {
                $order = json_decode($request->get('order'));
                $asc = $order->asc ?? false;
                $column = $order->column ?? 'ID';
                $query->orderBy($column, $asc ? 'asc' : 'desc');
            })
            ->when(!$request->has('order'), function ($query) use ($request) {
                $query->orderBy('ID', 'desc');
            })
            ->when($request->has('sort_by'), function ($query) use ($request) {
                switch ($request->get('sort_by')) {
                    case '1':
                        $query->orderBy('Customer.created_at', 'desc');
                        break;
                    case '2':
                        $query->orderBy('Customer.created_at', 'asc');
                        break;
                    default:
                        $query->addSelect(DB::raw('(select max("Job"."created_at") from Job where Job.CustomerID = Customer.ID) as recent'))
                            ->orderByRaw('"recent" desc');
                        break;
                }
            })
            ->when($request->has('unpaid'), function ($query) use($request) {
                if($request->get('unpaid') == '1') {
                    $query->havingRaw('sum("Job"."Price") > ?', [SettingController::get('unpaid_threshold')]);
                }
            })
            ->addSelect(DB::raw('sum("Job"."Price") as unpaid'))
            ->paginate($request->get('per_page') ?? 10, ['*'], 'page', $request->get('page') ?? 1);

        return response()
            ->json(['status' => 'success', 'data' => $customers]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {
        $data = $request->only('Name');
        $customer = Customer::create($data);
        $customer->load('jobs');

        if ($customer) {
            return response()->json(['status' => 'success', 'data' => $customer], 200);
        }

        return response()->json(['status' => 'error', 'message' => 'There was an error. Please try again.'], 500);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerRequest $request, $id)
    {
        $data = $request->only('field', 'value');
        $customer = Customer::find($id);
        if ($customer) {
            if ($customer->update([$data['field'] => $data['value']])) {
                $customer->load('jobs');
                return response()->json(['status' => 'success', 'data' => $customer]);
            }

            return response()->json(['status' => 'error', 'message' => 'There was an error. Please try again.'], 500);
        }

        return response()->json(['status' => 'error', 'message' => 'Customer not found. Please try again.'], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Customer::find($id);
        if ($customer) {
            if ($customer->delete()) {
                return response()->json(['status' => 'success', 'data' => $customer]);
            }

            return response()->json(['status' => 'error', 'message' => 'There was an error. Please try again.'], 500);
        }

        return response()->json(['status' => 'error', 'message' => 'Customer not found. Please try again.'], 404);
    }
}
