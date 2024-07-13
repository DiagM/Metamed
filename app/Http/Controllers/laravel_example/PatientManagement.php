<?php

namespace App\Http\Controllers\laravel_example;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Models\Disease;
use App\Models\diseases;
use App\Models\MedicalFile;
use App\Models\Reservation;
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
    $user = Auth::user();

    if ($user->hasRole('doctor')) {
      // Retrieve patients of the doctor
      $patients = $user->patients;
      $filterdoctors = $user;
    } elseif ($user->hasRole('hospital')) {
      // Retrieve departments of the hospital
      $departments = $user->departments()->with('doctorsdepartment')->get();


      // Collect all patients of departments under the hospital
      $patients = collect();
      $filterdoctors = collect();
      foreach ($departments as $department) {
        $departmentDoctors = $department->doctorsdepartment;
        $filterdoctors = $filterdoctors->merge($departmentDoctors);
        foreach ($departmentDoctors as $doctor) {
          $patients = $patients->merge($doctor->patients);
        }
      }
    } elseif ($user->hasRole('department')) {
      // Retrieve doctors of the department
      $doctors = $user->doctorsdepartment;
      $filterdoctors = $doctors;
      // Collect all patients of doctors in the department
      $patients = $doctors->pluck('patients')->flatten();
    } elseif ($user->hasRole('SuperAdmin')) {
      // Retrieve all users with the "patient" role
      $patientRole = Role::findByName('patient');
      $patients = User::whereHas('roles', function ($query) use ($patientRole) {
        $query->where('role_id', $patientRole->id);
      })->get();
      $doctorRole = Role::findByName('doctor');
      $filterdoctors = User::whereHas('roles', function ($query) use ($doctorRole) {
        $query->where('role_id', $doctorRole->id);
      })->get();

      $hospitalRole = Role::findByName('hospital');
      $departments = User::whereHas('roles', function ($query) use ($hospitalRole) {
        $query->where('role_id', $hospitalRole->id);
      })->with('departments.doctorsdepartment')->get();
    } else {
      // Handle other cases or defaults as per your application logic
      $patients = collect();
    }

    // Calculate statistics
    $totalPatients = $patients->count();
    $MalePatients = $patients->where('gender', 'male')->count();
    $FemalePatients = $patients->where('gender', 'female')->count();

    // Count blood types
    $bloodTypeCounts = $patients->groupBy('blood_type')->map->count();

    // Determine the most frequent blood type and its count
    $mostCommonBloodType = $bloodTypeCounts->sortDesc()->keys()->first();
    $mostCommonBloodTypeCount = $bloodTypeCounts->get($mostCommonBloodType);

    //disease
    $diseases = Disease::all();
    // Assuming $user is the authenticated user object
    if ($user->hasRole('hospital') || $user->hasRole('SuperAdmin')) {      // User has the role 'hospital', so include departments
      return view('content.laravel-example.patient-management', [
        'totalPatients' => $totalPatients,
        'MalePatients' => $MalePatients,
        'FemalePatients' => $FemalePatients,
        'mostCommonBloodType' => $mostCommonBloodType,
        'mostCommonBloodTypeCount' => $mostCommonBloodTypeCount,
        'filterdoctors' => $filterdoctors,
        'user' => $user,
        'departments' => $departments,
        'diseases' => $diseases,
      ]);
    } else {
      // User does not have the role 'hospital', do not include departments
      return view('content.laravel-example.patient-management', [
        'totalPatients' => $totalPatients,
        'MalePatients' => $MalePatients,
        'FemalePatients' => $FemalePatients,
        'mostCommonBloodType' => $mostCommonBloodType,
        'mostCommonBloodTypeCount' => $mostCommonBloodTypeCount,
        'filterdoctors' => $filterdoctors,
        'user' => $user,
        'diseases' => $diseases,
      ]);
    }
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
      6 => 'blood_type',
    ];

    // Get the "patient" role
    $patientRole = Role::findByName('patient');
    $user = Auth::user();

    if ($user->hasRole('doctor')) {
      // Retrieve patients related to the authenticated doctor
      $doctor = $user;
      $usersQuery = User::whereHas('doctors', function ($query) use ($doctor) {
        $query->where('doctor_id', $doctor->id);
      });
    } elseif ($user->hasRole('hospital')) {
      // Retrieve patients related to the authenticated hospital
      $hospital = $user;
      $departments = $hospital->departments;

      $patients = collect();
      foreach ($departments as $department) {
        $departmentDoctors = $department->doctorsdepartment;
        foreach ($departmentDoctors as $doctor) {
          $patients = $patients->merge($doctor->patients);
        }
      }
      // Create a query builder for the collected patients
      $usersQuery = User::whereIn('id', $patients->pluck('id'));
    } elseif ($user->hasRole('department')) {
      // Retrieve patients related to the authenticated hospital
      $department = $user;

      $patients = collect();

      $departmentDoctors = $department->doctorsdepartment;
      foreach ($departmentDoctors as $doctor) {
        $patients = $patients->merge($doctor->patients);
      }
      // Create a query builder for the collected patients
      $usersQuery = User::whereIn('id', $patients->pluck('id'));
    } else {
      // Initialize the user query builder with the "patient" role constraint
      $usersQuery = User::whereHas('roles', function ($query) use ($patientRole) {
        $query->where('role_id', $patientRole->id);
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
          ->orWhere('blood_type', 'LIKE', "%{$search}%"); // Add blood_type to search
      });
    }

    // Apply blood_type filter if provided
    if ($request->filled('blood_type')) {
      $bloodType = $request->input('blood_type');
      $usersQuery->where('blood_type', $bloodType);
    }

    // Apply doctor_name filter if provided
    if ($request->filled('doctor_name')) {
      $doctorId = $request->input('doctor_name');
      $usersQuery->whereHas('doctors', function ($query) use ($doctorId) {
        $query->where('doctor_id', $doctorId);
      });
    }

    // Apply department_name filter if provided
    if ($request->filled('department_name')) {
      $departmentName = $request->input('department_name');
      $usersQuery->whereHas('doctors.department', function ($query) use ($departmentName) {
        $query->where('id', 'LIKE', "%{$departmentName}%");
      });
    }
    if ($request->filled('hospital_name')) {
      $departmentName = $request->input('hospital_name');
      $usersQuery->whereHas('doctors.department.hospital', function ($query) use ($departmentName) {
        $query->where('id', 'LIKE', "%{$departmentName}%");
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
        'fake_id' => $start + $index + 1,
        'name' => $user->name,
        'email' => $user->email,
        'license_number' => $user->license_number,
        'contact' => $user->contact,
        'blood_type' => $user->blood_type,
        // Add other fields as needed
      ];
    }

    // Return JSON response for DataTable
    return response()->json([
      'draw' => intval($request->input('draw', 1)),
      'recordsTotal' => User::count(), // Total records without filtering
      'recordsFiltered' => $totalFiltered, // Total records with filtering
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
    if (Auth::user()->hasRole('doctor') || Auth::user()->hasRole('SuperAdmin')) {
      $doctor = Auth::user();

      // Check if the license number is unique, excluding the current user
      $existingUserWithLicense = User::where('license_number', $request->license_number)
        ->where('id', '!=', $userID)
        ->first();

      if ($existingUserWithLicense) {
        // If a user with the same license number exists and it's not the same user being updated
        if ($doctor->patients->contains($existingUserWithLicense)) {
          // If the authenticated doctor has a relationship with the patient
          return response()->json(['message' => "License number already exists"], 422);
        } else {
          // If the authenticated doctor does not have a relationship with the patient,
          // create the relationship
          $doctor->patients()->attach($existingUserWithLicense);
          return response()->json('Created');
        }
      }

      // Check if the email is unique, excluding the current user
      $existingUserWithEmail = User::where('email', $request->email)
        ->where('id', '!=', $userID)
        ->first();

      if ($existingUserWithEmail) {
        // If a user with the same email exists and it's not the same user being updated
        return response()->json(['message' => "Email already exists"], 422);
      }



      // If the user is newly created, attach them to the doctor
      if (!$userID) {
        //  create user
        $user = User::updateOrCreate(
          ['id' => $userID],
          [
            'name' => $request->name,
            'email' => $request->email,
            'contact' => $request->userContact,
            'license_number' => $request->license_number,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'address' => $request->address,
            'height' => $request->height,
            'weight' => $request->weight,
            'blood_type' => $request->blood_type,
            'medical_notes' => $request->medical_notes,
            'allergies' => $request->allergies,
            'password' => bcrypt(Str::random(10))
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

        SendEmailJob::dispatch($token, $user->email);
      } else {
        // Update the user
        $user = User::updateOrCreate(
          ['id' => $userID],
          [
            'name' => $request->name,
            'email' => $request->email,
            'contact' => $request->userContact,
            'license_number' => $request->license_number,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'address' => $request->address,
            'height' => $request->height,
            'weight' => $request->weight,
            'blood_type' => $request->blood_type,
            'medical_notes' => $request->medical_notes,
            'allergies' => $request->allergies,
          ]
        );
      }
      // Attach diseases if present in the request
      if ($request->has('diseases')) {
        $user->diseases()->sync($request->diseases);
      }
      return response()->json($userID ? 'Updated' : 'Created');
    }
  }



  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    $where = ['id' => $id];
    $user = User::with('diseases')->where($where)->first();
    $totalMedicalFiles = MedicalFile::where('patient_id', $id)->count();
    $totalVisits = Reservation::where('patient_id', $id)->where('doctor_id', auth()->id())->count();
    $reservations = Reservation::where('patient_id', $id)->where('doctor_id', auth()->id())->get();

    return view('content.laravel-example.MedicalFile', [
      'user' => $user,
      'TotalMedicalFiles' => $totalMedicalFiles,
      'TotalVisits' => $totalVisits,
      'reservations' => $reservations,
    ]);
  }


  /**
   * Show the form for editing the specified resource.
   */
  public function edit($id)
  {
    $user = User::with('diseases')->find($id);
    return response()->json($user);
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
    // Get the logged-in user
    $loggedInUser = auth()->user();

    // Remove the relationship
    $loggedInUser->patients()->detach($id);
  }

  //mobile
  public function patientfiles(Request $request)
  {
    $user = $request->user();
    $medicalFiles = $user->medicalFiles()->with('doctor')->get()->map(function ($file) {
      $filePath = $file->file_path;

      // Extract file name
      $fileName = basename($filePath);
      // Determine file type (MIME type)
      $fileType = mime_content_type(storage_path('app/' . $filePath)); // Using mime_content_type()

      return [
        'id' => $file->id,
        'name' => $file->file_name,
        'description' => $file->description,
        'date' => $file->created_at->format('Y-m-d H:i'),
        'file_name' => $fileName,
        'doctor' => $file->doctor ? [
          'id' => $file->doctor->id,
          'name' => $file->doctor->name,
          'department' => $file->doctor->department ? [
            'id' => $file->doctor->department->id,
            'name' => $file->doctor->department->name,
            'hospital' => $file->doctor->department->hospital ? [
              'id' => $file->doctor->department->hospital->id,
              'name' => $file->doctor->department->hospital->name,
            ] : null,
          ] : null,
        ] : null,
      ];
    });

    return response()->json($medicalFiles);
  }
}
