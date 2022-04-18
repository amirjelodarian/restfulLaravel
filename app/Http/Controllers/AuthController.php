<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $request->merge([
            'password' => Hash::make($request->password)
        ]);
        $user = User::create($request->all());
        $token = $user->createToken('myapptoken')->plainTextToken;
        return response()->json([
            'user'  => $user,
            'token' => $token
        ], Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request)
    {
        $user = User::whereEmail($request->input('email'))->first();


        if(!$user || !Hash::check($request->input('password'), $user->password))
            return response()->json(['message' => 'Bad creds'], Response::HTTP_UNAUTHORIZED);

        //else
        $token = $user->createToken('myapptoken')->plainTextToken;
        return response()->json([
            'user'  => $user,
            'token' => $token
        ], Response::HTTP_CREATED);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return response()->json(['message' => 'logout'], Response::HTTP_OK);
    }
}
