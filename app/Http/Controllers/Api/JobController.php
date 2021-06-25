<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Job\AddJobRequest;
use App\Models\Customer;
use App\Models\Job;
use App\Models\Method;
use App\Models\Type;
use Carbon\Carbon;
use Illuminate\Http\Request;
use GuzzleHttp;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jobs = Job::with('method', 'customer', 'type')
            ->get();
        return response()->json(['status' => 'success', 'data' => $jobs]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddJobRequest $request)
    {
        $data = $request->only('name', 'customer', 'type', 'method', 'start_date', 'finish_date', 'price_yen', 'note');

        $data['customer_id'] = $data['customer'];
        $data['type_id'] = $data['type'];
        $data['method_id'] = $data['method'];
        $data['start_date'] = Carbon::parse($data['start_date']);
        $data['finish_date'] = Carbon::parse($data['finish_date']);

        unset($data['customer'], $data['type'], $data['method']);

        $rate = $this->fetchRate();
        if($rate) {
            $data['price'] = round((1 / $rate) * $data['price_yen']);
        }

        if($job = Job::create($data)) {
            $job->refresh();
            $job->load('customer', 'method', 'type');
            return response()
                ->json(['data' => $job, 'status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => 'There was an error. Please try again.'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $data = $request->only('field', 'value');

        $job = Job::with('customer', 'type', 'method')
            ->where('id', $id)
            ->first();

        switch ($data['field']) {
            case 'customer' :
                $data['field'] = 'customer_id';
                break;
            case 'type':
                $data['field'] = 'type_id';
                break;
            case 'method':
                $data['field'] = 'method_id';
                break;
            default:
                break;
        }

        $updateData[$data['field']] = $data['value'];

        if ($data['field'] === 'price_yen') {
            $updateData['price'] = round($data['value'] / $this->fetchRate(), 0);
        }

        if (in_array($data['field'], ['start_date', 'pay_date', 'deadline', 'finish_date'])) {
            $updateData[$data['field']] = Carbon::parse($data['value']);
        }

        if ($job->update($updateData)) {
            return response()->json(['data' => $job->refresh(), 'status' => 'success']);
        }

        return response()->json(['status' => 'error']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $job = Job::find($id);
        if ($job) {
            if ($job->delete()) {
                return response()
                    ->json(['data' => $job, 'status' => 'success'], 200);
            }
            return response()->json(['status' => 'error', 'message' => 'There was an error. Please try again.'], 500);
        }
        return response()->json(['status' => 'error', 'message' => 'Job not found. Please try again.'], 404);
    }

    public function additions()
    {
        $methods = Method::withTrashed()->orderBy('name')->get();
        $types = Type::withTrashed()->orderBy('name')->get();
        $customers = Customer::withTrashed()->orderBy('name')->get();

        return response()->json(['status' => 'success', 'data' => [
            'customers' => $customers,
            'types' => $types,
            'methods' => $methods,
        ]]);
    }

    protected function fetchRate()
    {
        try {
            $client = new GuzzleHttp\Client();
            $response = $client->request('get', env('EXCHANGE_RATE'));
            $string = $response->getBody()->getContents();

            $index = preg_match("/<Cube currency='JPY' rate='(.*?)'\/>/m", $string, $jpy);
            $jpy = $jpy[$index];

            $index = preg_match("/<Cube currency='USD' rate='(.*?)'\/>/m", $string, $usd);
            $usd = $usd[$index];

            return $jpy / $usd;
        } catch (\Exception $e) {
            return -1;
        }
    }

    public function getRate()
    {
        $rate = $this->fetchRate();
        if ($rate > 0) {
            return response()->json(['status' => 'success', 'data' => $rate]);
        }
        return response()->json(['status' => 'error']);
    }
}
