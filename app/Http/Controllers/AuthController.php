<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
  public function login(Request $request)
  {
      $request->validate([
          'email' => 'required|email',
          'password' => 'required|string',
      ]);

      $credentials = $request->only('email', 'password');
      if (Auth::attempt($credentials)) {
          $user = Auth::user();
          $token = $user->createToken('Personal Access Token')->plainTextToken;

          return response()->json([
              'status' => 'success',
              'user' => $user,
              'token' => $token,
          ]);
      } else {
          return response()->json(['status' => 'error', 'message' => 'Invalid Credentials'], 401);
      }
  }

  public function logout()
  {
    auth()->user()->tokens()->delete();
    return response()->json(['message' => 'Log out']);

  }
}
