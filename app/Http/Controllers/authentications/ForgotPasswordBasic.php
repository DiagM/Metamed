<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
    $token = Str::random(60);
    DB::table('password_reset_tokens')->updateOrInsert(
      ['email' => $request->email],
      ['token' => $token, 'created_at' => now()]
    );
    SendEmailJob::dispatch($token, $request->email);
    return redirect()->back()->with('success', 'Password reset email has been sent.');
  }
}
