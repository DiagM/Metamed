<?php

namespace App\Http\Controllers\laravel_example;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RTippin\Messenger\Facades\Messenger;
use Spatie\Permission\Models\Role;

class UserManagement extends Controller
{
  /**
   * Redirect to user-management view.
   *
   */
  public function UserManagement()
  {
    $user = Auth::user();
    // Get the "doctor" role
    $doctorRole = Role::findByName('doctor');

    if ($user->hasRole('hospital')) {
      // Retrieve departments of the hospital
      $departments = $user->departments;
      $doctors = collect();
      foreach ($departments as $department) {
        $departmentDoctors = $department->doctorsdepartment;
        $doctors = $doctors->merge($departmentDoctors);
      }
    } elseif ($user->hasRole('department')) {
      // Retrieve doctors of the department
      $doctors = $user->doctorsdepartment;
    } elseif ($user->hasRole('SuperAdmin')) {

      $doctorRole = Role::findByName('doctor');
      $doctors = User::whereHas('roles', function ($query) use ($doctorRole) {
        $query->where('role_id', $doctorRole->id);
      })->get();

      $hospitalRole = Role::findByName('hospital');
      $hospitals = User::whereHas('roles', function ($query) use ($hospitalRole) {
        $query->where('role_id', $hospitalRole->id);
      })->get();
      $departmentrole = Role::findByName('department');

      $departments = User::whereHas('roles', function ($query) use ($departmentrole) {
        $query->where('role_id', $departmentrole->id);
      })->get();
    }

    // Calculate the number of reservations per doctor
    $doctors->each(function ($doctor) {
      $doctor->reservation_count = $doctor->doctorReservations()->count();
    });

    // Find the doctor with the most reservations
    $doctorWithMostReservations = $doctors->sortByDesc('reservation_count')->first();
    $mostReservationsCount = $doctorWithMostReservations ? $doctorWithMostReservations->reservation_count : 0;

    // Find the doctor with the least reservations
    $doctorWithLeastReservations = $doctors->sortBy('reservation_count')->first();
    $leastReservationsCount = $doctorWithLeastReservations ? $doctorWithLeastReservations->reservation_count : 0;

    if ($user->hasRole('department')) {
      return view('content.laravel-example.user-management', [
        'totalDoctors' => $doctors->count(),
        'mostReservationsDoctor' => $doctorWithMostReservations,
        'mostReservationsCount' => $mostReservationsCount,
        'leastReservationsDoctor' => $doctorWithLeastReservations,
        'leastReservationsCount' => $leastReservationsCount,
      ]);
    } elseif ($user->hasRole('hospital')) {
      return view('content.laravel-example.user-management', [
        'totalDoctors' => $doctors->count(),
        'mostReservationsDoctor' => $doctorWithMostReservations,
        'mostReservationsCount' => $mostReservationsCount,
        'leastReservationsDoctor' => $doctorWithLeastReservations,
        'leastReservationsCount' => $leastReservationsCount,
        'departments' => $departments,
      ]);
    } elseif ($user->hasRole('SuperAdmin')) {
      return view('content.laravel-example.user-management', [
        'totalDoctors' => $doctors->count(),
        'mostReservationsDoctor' => $doctorWithMostReservations,
        'mostReservationsCount' => $mostReservationsCount,
        'leastReservationsDoctor' => $doctorWithLeastReservations,
        'leastReservationsCount' => $leastReservationsCount,
        'hospitals' => $hospitals
      ]);
    }
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

    $user = Auth::user();
    $doctorRole = Role::where('name', 'doctor')->first();
    // Get the "doctor" role
    if ($user->hasRole('department')) {


      // Initialize the user query builder with the "department" role constraint
      $usersQuery = User::whereHas('roles', function ($query) use ($doctorRole) {
        $query->where('id', $doctorRole->id);
      })->where('department_id', Auth::id())->with('doctorsdepartment');
    } elseif ($user->hasRole('hospital')) {
      $hospital = $user;
      $departments = $hospital->departments;

      $doctors = collect();
      foreach ($departments as $department) {
        $departmentDoctors = $department->doctorsdepartment;
        $doctors = $doctors->merge($departmentDoctors);
      }
      // Create a query builder for the collected patients
      $usersQuery = User::whereIn('id', $doctors->pluck('id'));
    } elseif ($user->hasRole('SuperAdmin')) {
      // Initialize the user query builder with the "patient" role constraint
      $usersQuery = User::whereHas('roles', function ($query) use ($doctorRole) {
        $query->where('role_id', $doctorRole->id);
      });
    }
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
    // Apply department_name filter if provided
    if ($request->filled('department_name')) {
      $departmentName = $request->input('department_name');
      $usersQuery->whereHas('department', function ($query) use ($departmentName) {
        $query->where('id', 'LIKE', "%{$departmentName}%");
      });
    }
    if ($request->filled('hospital_name')) {
      $departmentName = $request->input('hospital_name');
      $usersQuery->whereHas('department.hospital', function ($query) use ($departmentName) {
        $query->where('id', 'LIKE', "%{$departmentName}%");
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
        Messenger::getProviderMessenger($user);

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

  public function indexmobile()
  {
    return User::all();
  }
  public function userroles(Request $request)
  {
    return response()->json([
      'roles' => $request->user()->getRoleNames(),
    ]);
  }
}
