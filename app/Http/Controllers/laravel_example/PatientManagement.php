<?php

namespace App\Http\Controllers\laravel_example;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PatientManagement extends Controller
{
  public function patientManagement()
  {
    $users = User::all();
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

    $search = [];

    $totalData = User::count();

    $totalFiltered = $totalData;

    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');

    if (empty($request->input('search.value'))) {
      $users = User::offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();
    } else {
      $search = $request->input('search.value');

      $users = User::where('id', 'LIKE', "%{$search}%")
        ->orWhere('name', 'LIKE', "%{$search}%")
        ->orWhere('email', 'LIKE', "%{$search}%")
        ->orWhere('license_number', 'LIKE', "%{$search}%")
        ->offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();

      $totalFiltered = User::where('id', 'LIKE', "%{$search}%")
        ->orWhere('name', 'LIKE', "%{$search}%")
        ->orWhere('email', 'LIKE', "%{$search}%")
        ->orWhere('license_number', 'LIKE', "%{$search}%")
        ->count();
    }

    $data = [];

    if (!empty($users)) {
      // providing a dummy id instead of database ids
      $ids = $start;

      foreach ($users as $user) {
        $nestedData['id'] = $user->id;
        $nestedData['fake_id'] = ++$ids;
        $nestedData['name'] = $user->name;
        $nestedData['email'] = $user->email;
        $nestedData['license_number'] = $user->license_number;
        $nestedData['contact'] = $user->contact;


        $data[] = $nestedData;
      }
    }

    if ($data) {
      return response()->json([
        'draw' => intval($request->input('draw')),
        'recordsTotal' => intval($totalData),
        'recordsFiltered' => intval($totalFiltered),
        'code' => 200,
        'data' => $data,
      ]);
    } else {
      return response()->json([
        'message' => 'Internal Server Error',
        'code' => 500,
        'data' => [],
      ]);
    }
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
    // Check if the license number is unique
    $existingUserWithLicense = User::where('license_number', $request->license_number)->first();

    if ($existingUserWithLicense && $existingUserWithLicense->id !== $userID) {
      // If a user with the same license number exists and it's not the same user being updated
      return response()->json(['message' => "License number already exists"], 422);
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
