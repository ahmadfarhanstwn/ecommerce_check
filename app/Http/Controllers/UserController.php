<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index() {
        return response()->json(User::with(['orders'])->get());
    }

    public function register(Request $request) {
        $validated = Validator::make($request->all(), [
            'name' => 'required|max:50',
            'email' => 'email|required|unique:users',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validated->fails()) {
            return response()->json(['error' => $validated->errors()], 401);
        }

        $data = $request->only(['name', 'email', 'password']);
        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);
        $user->is_admin = 0;

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('bigStore')->accessToken,
        ]);
    }

    public function login(Request $request) {
        $status = 401;
        $response = ['error' => 'Unauthorized'];

        if (Auth::attempt($request->only(['name', 'password']))) {
            $status = 200;
            $response = [
                'user' => Auth::user(),
                'token' => Auth::user()->createToken('bigStore')->accessToken
            ];
        }

        return response()->json($response, $status);
    }

    public function show(User $user) {
        return response()->json($user);
    }

    public function showOrders(User $user) {
        return response()->json($user->orders()->with(['product'])->get());
    }
}
