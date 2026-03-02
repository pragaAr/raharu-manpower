<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

      if (!$user->karyawan || $user->karyawan->status !== 'aktif') {
        return response()->json([
          'message' => 'Akun tidak aktif.'
        ], 403);
      }

      $user->tokens()->delete();

      $token = $user->createToken('mobile-token', ['*'], now()->addDays(30))->plainTextToken;

      return response()->json([
        'message' => 'Login success',
        'token' => $token,
        'user' => $user->load('karyawan.lokasi', 'karyawan.jabatan'),
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
    $user = $request->user()->load('karyawan.lokasi', 'karyawan.jabatan');

    return response()->json($user);
  }
}
