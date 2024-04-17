<?php

namespace App\Http\Controllers\laravel_example;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;


class PatientManagement extends Controller
{
  public function patientManagement()
  {
    // Get the "patient" role
    $patientRole = Role::findByName('patient');
    if (Auth::user()->hasRole('doctor')) {
      $doctor = Auth::user();
      $users = $doctor->patients()->get();
    } else {
      // Retrieve users with the "patient" role
      $users = User::whereHas('roles', function ($query) use ($patientRole) {
        $query->where('role_id', $patientRole->id);
      })->get();
    }

    $userCount = $users->count();
    $verified = User::whereNotNull('email_verified_at')->get()->count();
    $notVerified = User::whereNull('email_verified_at')->get()->count();
    $usersUnique = $users->unique(['email']);
    $userDuplicates = $users->diff($usersUnique)->count();

    return view('content.laravel-example.patient-management', [
      'totalUser' => $userCount,
      'verified' => $verified,
      'notVerified' => $notVerified,
      'userDuplicates' => $userDuplicates,
    ]);
  }
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $columns = [
      1 => 'id',
      2 => 'name',
      3 => 'email',
      4 => 'license_number',
      5 => 'contact',
    ];
    // Get the "department" role
    $departmentRole = Role::findByName('patient');
    if (Auth::user()->hasRole('doctor')) {
      $doctor = Auth::user();
      // Retrieve patients related to the authenticated doctor
      $usersQuery = User::whereHas('patients', function ($query) use ($doctor) {
        // Filter patients based on the relationship with the authenticated doctor
        $query->where('users.id', $doctor->id);
      });
    } else {
      // Initialize the user query builder with the "department" role constraint
      $usersQuery = User::whereHas('roles', function ($query) use ($departmentRole) {
        $query->where('role_id', $departmentRole->id);
      });
    }






    // Apply search filter if provided
    if ($request->filled('search.value')) {
      $search = $request->input('search.value');
      $usersQuery->where(function ($query) use ($search) {
        $query->where('id', 'LIKE', "%{$search}%")
          ->orWhere('name', 'LIKE', "%{$search}%")
          ->orWhere('email', 'LIKE', "%{$search}%")
          ->orWhere('license_number', 'LIKE', "%{$search}%");
      });
    }

    // Get the total count of filtered records
    $totalFiltered = $usersQuery->count();

    // Apply pagination and ordering
    $start = $request->input('start', 0);
    $limit = $request->input('length', 10);
    $orderColumn = $columns[$request->input('order.0.column', 1)];
    $orderDirection = $request->input('order.0.dir', 'asc');
    $users = $usersQuery->offset($start)
      ->limit($limit)
      ->orderBy($orderColumn, $orderDirection)
      ->get();

    // Prepare data for DataTables response
    $data = [];
    foreach ($users as $index => $user) {
      $data[] = [
        'id' => $user->id,
        'fake_id' => $start + $index + 1, // Generate a unique identifier for the record
        'name' => $user->name,
        'email' => $user->email,
        'license_number' => $user->license_number,
        'contact' => $user->contact,
      ];
    }

    // Return JSON response
    return response()->json([
      'draw' => intval($request->input('draw')),
      'recordsTotal' => $users->count(), // Total records in the users table
      'recordsFiltered' => $totalFiltered,
      'code' => 200,
      'data' => $data,
    ]);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $userID = $request->id;
    if (Auth::user()->hasRole('doctor')) {
      $doctor = Auth::user();
      // Check if the license number is unique
      $existingUserWithLicense = User::where('license_number', $request->license_number)->first();

      if ($existingUserWithLicense && $existingUserWithLicense->id !== $userID) {
        // If a user with the same license number exists and it's not the same user being updated

        // Check if the authenticated user has a relationship with $existingUserWithLicense

        if ($doctor->patients->contains($existingUserWithLicense)) {
          // If the authenticated doctor does  have a relationship with the patient
          return response()->json(['message' => "License number already exists"], 422);
        } else {
          // If the authenticated doctor does not have a relationship with the patient,
          // create the relationship
          $doctor->patients()->attach($existingUserWithLicense);
          return response()->json('Created');
        }
      }


      if ($userID) {

        // update the value
        $users = User::updateOrCreate(
          ['id' => $userID],
          [
            'name' => $request->name, 'email' => $request->email, 'contact' => $request->userContact,
            'license_number' => $request->license_number, 'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender, 'address' => $request->address,
          ]
        );

        // user updated
        return response()->json('Updated');
      } else {
        // create new one if email is unique
        $userEmail = User::where('email', $request->email)->first();

        if (empty($userEmail)) {
          $user = User::updateOrCreate(
            ['id' => $userID],
            [
              'name' => $request->name, 'email' => $request->email, 'password' => bcrypt(Str::random(10)),
              'contact' => $request->userContact, 'license_number' => $request->license_number,
              'gender' => $request->gender, 'address' => $request->address,
              'date_of_birth' => $request->date_of_birth,

            ]
          );
          $doctor->patients()->attach($user);
          $user->assignRole('patient');
          // Send password reset email
          $token = Str::random(60);

          DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $token, 'created_at' => now()]
          );

          // $user->notify(new HospitalPasswordReset($token));
          SendEmailJob::dispatch($token, $user->email);

          // user created
          return response()->json('Created');
        } else {
          // user already exist
          return response()->json(['message' => "Email already exists"], 422);
        }
      }
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    $where = ['id' => $id];
    $user = User::where($where)->first();
    return view('content.laravel-example.MedicalFile', ['user' => $user]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit($id)
  {
    $where = ['id' => $id];

    $users = User::where($where)->first();

    return response()->json($users);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy($id)
  {
    $users = User::where('id', $id)->delete();
  }
}
