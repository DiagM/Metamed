<?php

namespace App\Http\Controllers\laravel_example;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserManagement extends Controller
{
  /**
   * Redirect to user-management view.
   *
   */
  public function UserManagement()
  {
    // Get the "doctor" role
    $doctorRole = Role::findByName('doctor');


    // Retrieve users with the "doctor" role
    $users = User::whereHas('roles', function ($query) use ($doctorRole) {
      $query->where('role_id', $doctorRole->id);
    })->where('department_id', Auth::id())->get();
    $userCount = $users->count();
    $verified = User::whereNotNull('email_verified_at')->get()->count();
    $notVerified = User::whereNull('email_verified_at')->get()->count();
    $usersUnique = $users->unique(['email']);
    $userDuplicates = $users->diff($usersUnique)->count();

    return view('content.laravel-example.user-management', [
      'totalUser' => $userCount,
      'verified' => $verified,
      'notVerified' => $notVerified,
      'userDuplicates' => $userDuplicates,
    ]);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $columns = [
      1 => 'id',
      2 => 'name',
      3 => 'email',
      4 => 'license_number',
      5 => 'department',
    ];

    // Get the "department" role
    $departmentRole = Role::where('name', 'doctor')->first();

    // Initialize the user query builder with the "department" role constraint
    $usersQuery = User::whereHas('roles', function ($query) use ($departmentRole) {
      $query->where('id', $departmentRole->id);
    })->where('department_id', Auth::id())->with('department');

    // Apply search filter if provided
    if ($request->filled('search.value')) {
      $search = $request->input('search.value');
      $usersQuery->where(function ($query) use ($search) {
        $query->where('id', 'LIKE', "%{$search}%")
          ->orWhere('name', 'LIKE', "%{$search}%")
          ->orWhere('email', 'LIKE', "%{$search}%")
          ->orWhere('license_number', 'LIKE', "%{$search}%")
          ->orWhereHas('department', function ($query) use ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
          });
      });
    }


    // Get the total count of filtered records before pagination
    $totalFiltered = $usersQuery->count();

    // Apply pagination and ordering
    $start = $request->input('start', 0);
    $limit = $request->input('length', 10);
    $orderColumnIndex = $request->input('order.0.column', 1);
    $orderDirection = $request->input('order.0.dir', 'asc');

    // Determine the column to order by
    $orderColumn = $columns[$orderColumnIndex];

    // If ordering by department name
    if ($orderColumn == 'department') {
      // Order by department name (assuming department name is stored in the users table)
      $usersQuery->orderBy('department_id', $orderDirection);
    } else {
      // Otherwise, order by other columns
      $usersQuery->orderBy($orderColumn, $orderDirection);
    }

    $users = $usersQuery->offset($start)
      ->limit($limit)
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
        'department' => $user->department->name,
      ];
    }

    // Return JSON response
    return response()->json([
      'draw' => intval($request->input('draw')),
      'recordsTotal' => User::count(), // Total records in the users table
      'recordsFiltered' => $totalFiltered,
      'code' => 200,
      'data' => $data,
    ]);
  }



  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $userID = $request->id;

    if ($userID) {
      // update the value
      $users = User::updateOrCreate(
        ['id' => $userID],
        [
          'name' => $request->name, 'email' => $request->email, 'contact' => $request->userContact,
          'license_number' => $request->license_number, 'department_id' => Auth::id()
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
            'contact' => $request->userContact, 'license_number' => $request->license_number, 'department_id' => Auth::id()
          ]
        );
        $user->assignRole('doctor');
        // Send password reset email
        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
          ['email' => $user->email],
          ['token' => $token, 'created_at' => now()]
        );

        // $user->notify(new doctorPasswordReset($token));
        SendEmailJob::dispatch($token, $user->email);
        // user created
        return response()->json('Created');
      } else {
        // user already exist
        return response()->json(['message' => "already exits"], 422);
      }
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $where = ['id' => $id];

    $users = User::where($where)->first();

    return response()->json($users);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $users = User::where('id', $id)->delete();
  }
}
