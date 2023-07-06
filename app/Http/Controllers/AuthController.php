<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AuthController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function signup(Request $request){

        $this->validate($request, [
            'username' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        return response()->json(['message' => 'Signup successful'], 201);
    }

    public function login(Request $request){
        $credentials = $request->only(['username', 'password']);
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            $is_admin = $user->hasRole('admin');
            $token = $user->createToken('auth-token')->plainTextToken;
            return response()->json(['token' => $token, 'user' => $user, 'is_admin' => $is_admin], 200);

        }
            return response()->json(['message', 'Invalid credentials'], 401);

    }

    public function logout(Request $request){
        $user = $request->user();

        $user->tokens()->delete();

        return response()->json(['message' => "Logout successful"]);
    }
}
