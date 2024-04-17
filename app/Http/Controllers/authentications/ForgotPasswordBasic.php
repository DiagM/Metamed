<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ForgotPasswordBasic extends Controller
{
  public function index()
  {
    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.authentications.auth-forgot-password-basic', ['pageConfigs' => $pageConfigs]);
  }

  public function reset(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' => 'required|email',
    ]);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    // Check if a user exists with the provided email
    $user = User::where('email', $request->email)->first();

    if (!$user) {
      return redirect()->back()->with('error', 'User with this email does not exist.');
    }
    $token = Str::random(60);
    DB::table('password_reset_tokens')->updateOrInsert(
      ['email' => $request->email],
      ['token' => $token, 'created_at' => now()]
    );
    SendEmailJob::dispatch($token, $request->email);
    return redirect()->back()->with('success', 'Password reset email has been sent.');
  }
}
