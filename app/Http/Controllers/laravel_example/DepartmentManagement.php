<?php

namespace App\Http\Controllers\laravel_example;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use RTippin\Messenger\Facades\Messenger;

class DepartmentManagement extends Controller
{
  public function departmentManagement()
  {
    $departmentRole = Role::findByName('department');
    $user = Auth::user();

    // Retrieve departments based on the user's role
    if ($user->hasRole('hospital')) {
      $departments = User::whereHas('roles', function ($query) use ($departmentRole) {
        $query->where('role_id', $departmentRole->id);
      })->where('hospital_id', $user->id)->get();
    } elseif ($user->hasRole('SuperAdmin')) {
      $departments = User::whereHas('roles', function ($query) use ($departmentRole) {
        $query->where('role_id', $departmentRole->id);
      })->get();
    } else {
      $departments = collect(); // Return an empty collection if the user doesn't have the appropriate role
    }

    $userCount = $departments->count();

    // Calculate the number of doctors for each department
    $departmentsWithDoctorCount = $departments->map(function ($department) {
      $department->doctor_count = $department->doctorsdepartment()->count();
      return $department;
    });

    // Find the department with the most doctors
    $mostDoctorsDepartment = $departmentsWithDoctorCount->sortByDesc('doctor_count')->first();

    // Find the department with the least doctors
    $leastDoctorsDepartment = $departmentsWithDoctorCount->sortBy('doctor_count')->first();

    // Get the count of doctors for the department with the most doctors
    $mostDoctorsDepartmentCount = $mostDoctorsDepartment ? $mostDoctorsDepartment->doctor_count : 0;
    $leastDoctorsDepartmentCount = $leastDoctorsDepartment ? $leastDoctorsDepartment->doctor_count : 0;

    return view('content.laravel-example.department-management', [
      'totalUser' => $userCount,
      'mostDoctorsDepartment' => $mostDoctorsDepartment,
      'mostDoctorsDepartmentCount' => $mostDoctorsDepartmentCount,
      'leastDoctorsDepartment' => $leastDoctorsDepartment,
      'leastDoctorsDepartmentCount' => $leastDoctorsDepartmentCount,
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
      4 => 'created_at',
      5 => 'contact',
    ];

    // Get the "department" role
    $departmentRole = Role::findByName('department');

    // Initialize the user query builder with the "department" role constraint
    $usersQuery = User::whereHas('roles', function ($query) use ($departmentRole) {
      $query->where('role_id', $departmentRole->id);
    });

    // Apply search filter if provided
    if ($request->filled('search.value')) {
      $search = $request->input('search.value');
      $usersQuery->where(function ($query) use ($search) {
        $query->where('id', 'LIKE', "%{$search}%")
          ->orWhere('name', 'LIKE', "%{$search}%")
          ->orWhere('email', 'LIKE', "%{$search}%")
          ->orWhere('created_at', 'LIKE', "%{$search}%");
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
        'created_at' => $user->created_at,
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

    if ($userID) {

      // update the value
      $users = User::updateOrCreate(
        ['id' => $userID],
        [
          'name' => $request->name, 'email' => $request->email, 'contact' => $request->contact,
          'hospital_id' =>  Auth::id(),

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
            'contact' => $request->contact, 'hospital_id' =>  Auth::id(),

          ]
        );
        $user->assignRole('department', 'doctormanage');
        // Send password reset email
        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
          ['email' => $user->email],
          ['token' => $token, 'created_at' => now()]
        );
        Messenger::getProviderMessenger($user);

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
    //
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
