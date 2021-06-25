<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Type\TypeRequest;
use App\Models\Type;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $types = Type::all();
        return response()->json(['status' => 'success', 'data' => $types]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TypeRequest $request)
    {
        $data = $request->only('name');
        $type = Type::create($data);

        if ($type) {
            return response()->json(['status' => 'success', 'data' => $type], 200);
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
    public function update(TypeRequest $request, $id)
    {
        $data = $request->only('name');
        $type = Type::find($id);
        if ($type) {
            if ($type->update($data)) {
                return response()->json(['status' => 'success', 'data' => $type]);
            }

            return response()->json(['status' => 'error', 'message' => 'There was an error. Please try again.'], 500);
        }

        return response()->json(['status' => 'error', 'message' => 'Type not found. Please try again.'], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $type = Type::find($id);
        if ($type) {
            if ($type->delete()) {
                return response()->json(['status' => 'success', 'data' => $type]);
            }

            return response()->json(['status' => 'error', 'message' => 'There was an error. Please try again.'], 500);
        }

        return response()->json(['status' => 'error', 'message' => 'Type not found. Please try again.'], 404);
    }
}
