<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use YieldStudio\LaravelExpoNotifier\Storage\ExpoTokenStorageMysql;



class ExpoTokenController extends Controller
{
  protected $expoTokenStorage;

  public function __construct(ExpoTokenStorageMysql $expoTokenStorage)
  {
      $this->expoTokenStorage = $expoTokenStorage;
  }

  public function store(Request $request)
  {
      $request->validate([
          'token' => 'required|string',
      ]);

      $user = $request->user(); // Assuming you are using Sanctum or Passport for API authentication
      $token = $request->input('token');

      $expoToken = $this->expoTokenStorage->store($token, $user);

      return response()->json(['message' => 'Push token saved successfully', 'expoToken' => $expoToken]);
  }

  public function delete(Request $request)
{
  $request->validate([
    'token' => 'string',
]);

$user = $request->user(); // Assuming you are using Sanctum or Passport for API authentication

$tokens = $request->input('token');

$this->expoTokenStorage->delete($tokens);

return response()->json(['message' => 'Push tokens deleted successfully']);

}

}
