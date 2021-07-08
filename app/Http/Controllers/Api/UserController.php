<?php

namespace App\Http\Controllers\Api;

use App\Events\User\PasswordChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
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
        $user = auth('api')
            ->user();
        $users = User::when($user->isAdmin(), function ($query) use ($user) {
            if (!$user->isSuperAdmin()) {
                $query->whereRole('user');
            }
        })
            ->get();
        return response()
            ->json(['data' => $users, 'status' => 'success']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->only('name', 'username', 'email', 'password');
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        if ($user) {
            $user->refresh();
            return response()
                ->json(['data' => $user, 'status' => 'success', 'message' => 'The user was created successfully.']);
        }

        return response()
            ->json(['status' => 'error', 'message' => 'There was an error. Please try again.'], 500);
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

        $authUser = auth('api')->user();

        if ($user && $user->id !== 1) {
            if ($request->get('field') === 'role' && !$authUser->isSuperAdmin()) {
                return response()
                    ->json(['status' => 'error'], 403);
            }
            $data['field'] = $request->get('field');
            $data['value'] = $request->get('value');
            if ($user->update([$data['field'] => $data['value']])) {
                $user->refresh();

                return response()
                    ->json(['data' => $user, 'status' => 'success']);
            }

            return response()
                ->json(['status' => 'error'], 500);

            return response()
                ->json(['status' => 'error'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public
    function destroy($id)
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

    public
    function resetPassword($id)
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
