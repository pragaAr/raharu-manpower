<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
  public function login(Request $request)
  {
    $credentials = $request->validate([
      'username' => 'required',
      'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
      $user = Auth::user();
      $token = $user->createToken('auth-token')->plainTextToken;

      return response()->json([
        'message' => 'Login success',
        'token' => $token,
        'user' => $user,
      ]);
    }

    return response()->json(['message' => 'Invalid credentials'], 401);
  }

  public function logout(Request $request)
  {
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Logout success']);
  }

  public function me(Request $request)
  {
    return response()->json($request->user());
  }
}
