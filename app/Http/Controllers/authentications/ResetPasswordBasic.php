<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;

class ResetPasswordBasic extends Controller
{
  public function index()
  {
    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.authentications.auth-reset-password-basic', ['pageConfigs' => $pageConfigs]);
  }
  public function reset(Request $request)
  {
    // Get the token and email values from the request
    $token = $request->input('token');
    $email = $request->input('emailhidden');

    // Query the database table
    $passwordResetToken = DB::table('password_reset_tokens')
      ->where('token', $token)
      ->where('email', $email)
      ->first();

    if (!$passwordResetToken) {
      // If no password reset token found, handle accordingly, maybe return errors or redirect
      return redirect()->back()->withErrors(['error' => 'Invalid token or email']);
    } else {
      // If a matching token is found, update user password and redirect to the login route
      // You need to implement the logic to update the user password here
      // Assuming you have a User model
      $user = User::where('email', $email)->first();
      $user->password = Hash::make($request->input('password')); // Set $newPassword to the new password value
      $user->save();

      return redirect()->route('login')->with('success', 'Password updated successfully');
    }
  }
}
