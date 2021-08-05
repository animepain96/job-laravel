<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Method\MethodRequest;
use App\Models\Method;
use Illuminate\Http\Request;

class MethodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $methods = Method::when($request->has('q'), function ($query) use ($request) {
            $query->where('Name', 'like', '%' . $request->get('q') . '%');
        })
            ->when($request->has('order'), function ($query) use ($request) {
                $order = json_decode($request->get('order'));
                $asc = $order->asc ?? false;
                $column = $order->column ?? 'ID';
                $query->orderBy($column, $asc ? 'asc' : 'desc');
            })
            ->when(!$request->has('sorter'), function ($query) {
                $query->orderBy('ID', 'desc');
            })
            ->paginate($request->get('per_page') ?? 10, ['*'], 'page', $request->get('page') ?? 1);

        return response()
            ->json(['data' => $methods, 'status' => 'success']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(MethodRequest $request)
    {
        $data = $request->only('Name');
        $method = Method::create($data);

        if ($method) {
            return response()->json(['status' => 'success', 'data' => $method], 200);
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
    public function update(MethodRequest $request, $id)
    {
        $data = $request->only('Name');
        $method = Method::find($id);
        if ($method) {
            if ($method->update($data)) {
                return response()->json(['status' => 'success', 'data' => $method]);
            }

            return response()->json(['status' => 'error', 'message' => 'There was an error. Please try again.'], 500);
        }

        return response()->json(['status' => 'error', 'message' => 'Method not found. Please try again.'], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $method = Method::find($id);
        if ($method) {
            if ($method->delete()) {
                return response()->json(['status' => 'success', 'data' => $method]);
            }

            return response()->json(['status' => 'error', 'message' => 'There was an error. Please try again.'], 500);
        }

        return response()->json(['status' => 'error', 'message' => 'Method not found. Please try again.'], 404);
    }
}
