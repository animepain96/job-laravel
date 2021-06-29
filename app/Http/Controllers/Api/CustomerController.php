<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Customer::with('jobs')
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {
        $data = $request->only('name');
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
