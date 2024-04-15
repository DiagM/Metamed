<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginBasic extends Controller
{
  public function index()
  {
    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.authentications.auth-login-basic', ['pageConfigs' => $pageConfigs]);
  }
  public function login(Request $request)
  {
    // Determine whether the input is an email or username
    $field = filter_var($request->input('email-username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

    // Validate the form data
    $credentials = $request->validate([
      'email-username' => 'required|string',
      'password' => 'required|string',
    ]);

    $field = filter_var($request->input('email-username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
    // Debugging
    // Attempt to authenticate the user
    if (Auth::attempt([$field => $request->input('email-username'), 'password' => $request->input('password')])) {
      // Authentication successful
      return redirect('/');
    } else {
      // Authentication failed
      return redirect()->back()->withInput()->withErrors(['email-username' => 'Invalid credentials']);
    }
  }
  public function logout(Request $request)
  {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/auth/login-basic');
  }
}
