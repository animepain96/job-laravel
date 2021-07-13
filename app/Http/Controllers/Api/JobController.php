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
        $jobs = Job::with([
            'customer' => function ($query) {
                $query->withTrashed();
            }, 'type' => function ($query) {
                $query->withTrashed();
            }, 'method' => function ($query) {
                $query->withTrashed();
            }
        ])
            ->get();
        return response()->json(['status' => 'success', 'data' => $jobs]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddJobRequest $request)
    {
        $data = $request->only('Name', 'Customer', 'Type', 'Method', 'StartDate', 'FinishDate', 'PriceYen', 'Note');

        $data['CustomerID'] = $data['Customer'];
        $data['TypeID'] = $data['Type'];
        $data['MethodID'] = $data['Method'];
        $data['StartDate'] = Carbon::parse($data['StartDate']);
        $data['FinishDate'] = Carbon::parse($data['FinishDate']);

        unset($data['Customer'], $data['Type'], $data['Method']);

        $rate = $this->fetchRate();
        if ($rate) {
            $data['Price'] = round((1 / $rate) * $data['PriceYen']);
        }

        if ($job = Job::create($data)) {
            $job->refresh();
            $job->load('Customer', 'Method', 'Type');
            return response()
                ->json(['data' => $job, 'status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => 'There was an error. Please try again.'], 500);
    }

    public function update(Request $request, $id)
    {
        $data = $request->only('field', 'value');

        $job = Job::with([
            'customer' => function ($query) {
                $query->withTrashed();
            }, 'type' => function ($query) {
                $query->withTrashed();
            }, 'method' => function ($query) {
                $query->withTrashed();
            }
        ])
            ->where('ID', $id)
            ->first();

        switch ($data['field']) {
            case 'Customer' :
                $data['field'] = 'CustomerID';
                break;
            case 'Type':
                $data['field'] = 'TypeID';
                break;
            case 'Method':
                $data['field'] = 'MethodID';
                break;
            default:
                break;
        }

        $updateData[$data['field']] = $data['value'];

        if ($data['field'] === 'PriceYen') {
            $updateData['Price'] = round($data['value'] / $this->fetchRate(), 0);
        }

        if (in_array($data['field'], ['StartDate', 'Paydate', 'Deadline', 'FinishDate'])) {
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
        $methods = Method::withTrashed()->orderBy('Name')->get();
        $types = Type::withTrashed()->orderBy('Name')->get();
        $customers = Customer::withTrashed()->orderBy('Name')->get();

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
