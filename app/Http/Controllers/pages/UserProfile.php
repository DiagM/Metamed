<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserProfile extends Controller
{
  public function index()
  {
    return view('content.pages.pages-profile-user');
  }


  public function show($id)
  {
    $user = User::findOrFail($id); // Retrieve the user by ID or fail with a 404 error
    return view('content.pages.pages-profile-user', compact('user')); // Pass the user data to the view
  }
  public function update(Request $request, $userId)
  {
    // Validate incoming request
    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'email' => 'required|email|max:255',
      'contact' => 'required|string|max:20', // Adjust max length as needed
      'license_number' => 'nullable|string|max:50', // Adjust max length as needed
      'date_of_birth' => 'required|date_format:Y-m-d',
      'address' => 'required|string|max:255',
      'password' => 'nullable|string|min:6|confirmed',
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    // Find the user by ID
    $user = User::findOrFail($userId);

    // Update user fields
    $user->name = $request->input('name');
    $user->email = $request->input('email');
    $user->contact = $request->input('contact');
    $user->license_number = $request->input('license_number');
    $user->date_of_birth = $request->input('date_of_birth');
    $user->address = $request->input('address');

    if ($request->filled('password')) {
      $user->password = bcrypt($request->input('password'));
    }

    // Save the updated user
    $user->save();

    return response()->json(['message' => 'User profile updated successfully'], 200);
  }
}
