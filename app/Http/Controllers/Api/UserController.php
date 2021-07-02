<?php

namespace App\Http\Controllers\Api;

use App\Events\User\PasswordChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return response()
            ->json(['data' => $users, 'status' => 'success']);
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
    public function store(StoreUserRequest $request)
    {
        $data = $request->only('name', 'email', 'password');
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        if ($user) {
            $user->refresh();
            return response()
                ->json(['data' => $user, 'status' => 'success']);
        }

        return response()
            ->json(['status' => 'error']);
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

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::find($id);
        if ($user) {
            $data['field'] = $request->get('field');
            $data['value'] = $request->get('value');
            if ($user->update([$data['field'] => $data['value']])) {
                $user->refresh();

                return response()
                    ->json(['data' => $user, 'status' => 'success']);
            }

            return response()
                ->json(['status' => 'error'], 500);
        }

        return response()
            ->json(['status' => 'error'], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($id != '1') {
            $user = User::find($id);
            if ($user) {
                if ($user->delete()) {
                    return response()
                        ->json(['data' => $user, 'status' => 'success']);
                }

                return response()
                    ->json(['status' => 'error'], 500);
            }
        }

        return response()
            ->json(['status' => 'error'], 404);
    }

    public function resetPassword($id)
    {
        if ($id != '1') {
            $user = User::find($id);
            if ($user) {
                $password = Str::random(10);
                if ($user->update(['password' => Hash::make($password)])) {
                    event(new PasswordChanged($user, $password));
                    return response()
                        ->json(['data' => $user, 'status' => 'success']);
                }

                return response()
                    ->json(['status' => 'error'], 500);
            }
        }

        return response()
            ->json(['status' => 'error'], 404);
    }
}
