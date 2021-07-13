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
    public function index()
    {
        return Method::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
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
     * @param  int  $id
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
