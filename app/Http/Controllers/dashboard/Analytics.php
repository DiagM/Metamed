<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Disease;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;

class Analytics extends Controller
{
  public function index()
  {
    $user = Auth::user();
    $diseases = Disease::all();

    if ($user->hasRole('SuperAdmin')) {
      // Count all users
      $usersCount = User::count();

      // Count users with the 'patient' role
      $patientRole = Role::findByName('patient');
      $patientsCount = User::whereHas('roles', function ($query) use ($patientRole) {
        $query->where('role_id', $patientRole->id);
      })->count();

      // Count users with the 'hospital' role
      $hospitalRole = Role::findByName('hospital');
      $hospitalsCount = User::whereHas('roles', function ($query) use ($hospitalRole) {
        $query->where('role_id', $hospitalRole->id);
      })->count();

      // Count users with the 'doctor' role
      $doctorRole = Role::findByName('doctor');
      $doctorsCount = User::whereHas('roles', function ($query) use ($doctorRole) {
        $query->where('role_id', $doctorRole->id);
      })->count();

      return view('content.dashboard.dashboards-analytics', [
        'usersCount' => $usersCount,
        'patientsCount' => $patientsCount,
        'hospitalsCount' => $hospitalsCount,
        'doctorsCount' => $doctorsCount,
        'diseases' => $diseases,
      ]);
    } elseif ($user->hasRole('doctor')) {
      // Count patients related to the doctor
      $patientsCount = $user->patients()->count();

      // Find the patient with the most reservations related to that doctor and count them
      $patientWithMostReservations = $user->doctorReservations()
        ->select('patient_id', DB::raw('count(*) as total'))
        ->groupBy('patient_id')
        ->orderBy('total', 'desc')
        ->first();
      // Get the patient name and count of reservations
      $patientWithMostReservationsDetails = $patientWithMostReservations ? User::find($patientWithMostReservations->patient_id) : null;
      $patientWithMostReservationsName = $patientWithMostReservationsDetails ? $patientWithMostReservationsDetails->name : 'No patient found';
      $patientWithMostReservationsCount = $patientWithMostReservations ? $patientWithMostReservations->total : 0;

      // Count the total reservations related to that doctor in the current month
      $currentMonth = Carbon::now()->month;
      $currentYear = Carbon::now()->year;

      $totalReservationsCurrentMonth = $user->doctorReservations()
        ->whereMonth('created_at', $currentMonth)
        ->whereYear('created_at', $currentYear)
        ->count();

      $totalReservationsCount = $user->doctorReservations()
        ->count();
      return view('content.dashboard.dashboards-analytics', [
        'totalReservationsCount' => $totalReservationsCount,
        'patientsCount' => $patientsCount,
        'patientWithMostReservationsName' => $patientWithMostReservationsName,
        'patientWithMostReservationsCount' => $patientWithMostReservationsCount,
        'totalReservationsCurrentMonth' => $totalReservationsCurrentMonth,
        'diseases' => $diseases,
      ]);
    } elseif ($user->hasRole('hospital')) {
      // Count departments related to the hospital
      $departmentsCount = $user->departments()->count();
      // Count doctors related to the hospital
      $doctorsCount = $user->departments()->withCount('doctorsdepartment')->get()->sum('doctorsdepartment_count');

      // Count patients related to the hospital
      $patientsCount = $user->departments()->with('doctorsdepartment.patients')->get()->pluck('doctorsdepartment')->flatten()->pluck('patients')->flatten()->unique('id')->count();
      // Count total reservations related to the hospital
      $reservationsCount = $user->departments()->with('doctorsdepartment.doctorReservations')->get()
        ->pluck('doctorsdepartment')->flatten()->pluck('doctorReservations')->flatten()->count();

      return view('content.dashboard.dashboards-analytics', [
        'departmentsCount' => $departmentsCount,
        'doctorsCount' => $doctorsCount,
        'patientsCount' => $patientsCount,
        'totalReservationsCount' => $reservationsCount,
        'diseases' => $diseases,
      ]);
    } elseif ($user->hasRole('department')) {
      // Count doctors related to the department
      $doctorsCount = $user->doctorsdepartment()->count();

      // Count patients related to the department
      $patientsCount = $user->doctorsdepartment()->with('patients')->get()->pluck('patients')->flatten()->unique('id')->count();

      // Count total reservations related to doctors in the department
      $reservationsCount = $user->doctorsdepartment()->with('doctorReservations')->get()
        ->pluck('doctorReservations')->flatten()->count();

      // Find the doctor with the most reservations and count them
      $doctorWithMostReservations = $user->doctorsdepartment()->withCount('doctorReservations')
        ->orderBy('doctor_reservations_count', 'desc')
        ->first();

      $doctorWithMostReservationsName = $doctorWithMostReservations ? $doctorWithMostReservations->name : 'No doctor found';
      $doctorWithMostReservationsCount = $doctorWithMostReservations ? $doctorWithMostReservations->doctor_reservations_count : 0;
      return view('content.dashboard.dashboards-analytics', [
        'doctorWithMostReservationsName' => $doctorWithMostReservationsName,
        'doctorWithMostReservationsCount' => $doctorWithMostReservationsCount,
        'doctorsCount' => $doctorsCount,
        'patientsCount' => $patientsCount,
        'totalReservationsCount' => $reservationsCount,
        'diseases' => $diseases,
      ]);
    }
  }
  public function patientgender()
  {
    $user = Auth::user();
    $patientRole = Role::findByName('patient');

    if ($user->hasRole('SuperAdmin')) {
      // Count male patients
      $malePatientsCount = User::whereHas('roles', function ($query) use ($patientRole) {
        $query->where('role_id', $patientRole->id);
      })->where('gender', 'male')->count();

      // Count female patients
      $femalePatientsCount = User::whereHas('roles', function ($query) use ($patientRole) {
        $query->where('role_id', $patientRole->id);
      })->where('gender', 'female')->count();
    } elseif ($user->hasRole('doctor')) {
      // Count male patients related to the doctor
      $malePatientsCount = $user->patients()->where('gender', 'male')->count();

      // Count female patients related to the doctor
      $femalePatientsCount = $user->patients()->where('gender', 'female')->count();
    } elseif ($user->hasRole('department')) {
      // Count male patients related to the department
      $malePatientsCount = $user->doctorsdepartment()->with('patients')->get()
        ->pluck('patients')->flatten()->where('gender', 'male')->unique('id')->count();

      // Count female patients related to the department
      $femalePatientsCount = $user->doctorsdepartment()->with('patients')->get()
        ->pluck('patients')->flatten()->where('gender', 'female')->unique('id')->count();
    } elseif ($user->hasRole('hospital')) {
      // Count male patients related to the hospital
      $malePatientsCount = $user->departments()->with('doctorsdepartment.patients')->get()
        ->pluck('doctorsdepartment')->flatten()->pluck('patients')->flatten()->where('gender', 'male')->unique('id')->count();

      // Count female patients related to the hospital
      $femalePatientsCount = $user->departments()->with('doctorsdepartment.patients')->get()
        ->pluck('doctorsdepartment')->flatten()->pluck('patients')->flatten()->where('gender', 'female')->unique('id')->count();
    } else {
      // Default counts in case the user role does not match any of the above
      $malePatientsCount = 0;
      $femalePatientsCount = 0;
    }

    // Prepare the data for the chart
    $data = [
      'series' => [$malePatientsCount, $femalePatientsCount],
      'labels' => ['Male', 'Female']
    ];

    return response()->json($data);
  }

  //
  public function patientDiseaseChart(Request $request)
  {
    $diseaseId = $request->input('disease');
    $user = Auth::user();

    // Get the disease by name
    $disease = Disease::where('id', $diseaseId)->first();

    // Get the patient role
    $patientRole = Role::findByName('patient');

    // Initialize counts
    $malePatientsCount = 0;
    $femalePatientsCount = 0;

    if ($disease) {
      if ($user->hasRole('SuperAdmin')) {
        // Count male patients with the specified disease
        $malePatientsCount = User::whereHas('roles', function ($query) use ($patientRole) {
          $query->where('role_id', $patientRole->id);
        })->where('gender', 'male')
          ->whereHas('diseases', function ($query) use ($disease) {
            $query->where('disease_id', $disease->id);
          })->count();

        // Count female patients with the specified disease
        $femalePatientsCount = User::whereHas('roles', function ($query) use ($patientRole) {
          $query->where('role_id', $patientRole->id);
        })->where('gender', 'female')
          ->whereHas('diseases', function ($query) use ($disease) {
            $query->where('disease_id', $disease->id);
          })->count();
      } elseif ($user->hasRole('doctor')) {
        // Count male patients with the specified disease related to the doctor
        $malePatientsCount = $user->patients()->where('gender', 'male')
          ->whereHas('diseases', function ($query) use ($disease) {
            $query->where('disease_id', $disease->id);
          })->count();

        // Count female patients with the specified disease related to the doctor
        $femalePatientsCount = $user->patients()->where('gender', 'female')
          ->whereHas('diseases', function ($query) use ($disease) {
            $query->where('disease_id', $disease->id);
          })->count();
      } elseif ($user->hasRole('department')) {
        // Count male patients with the specified disease related to the department
        $malePatientsCount = $user->doctorsdepartment()->with('patients')
          ->whereHas('patients', function ($query) use ($diseaseId) {
            $query->where('gender', 'male')
              ->whereHas('diseases', function ($query) use ($diseaseId) {
                $query->where('disease_id', $diseaseId);
              });
          })->count();

        // Count female patients with the specified disease related to the department
        $femalePatientsCount = $user->doctorsdepartment()->with('patients')
          ->whereHas('patients', function ($query) use ($diseaseId) {
            $query->where('gender', 'female')
              ->whereHas('diseases', function ($query) use ($diseaseId) {
                $query->where('disease_id', $diseaseId);
              });
          })->count();
      } elseif ($user->hasRole('hospital')) {
        // Count male patients with the specified disease related to the hospital
        $malePatientsCount = $user->departments()
          ->whereHas('doctorsdepartment.patients', function ($query) use ($diseaseId) {
            $query->where('gender', 'male')
              ->whereHas('diseases', function ($query) use ($diseaseId) {
                $query->where('disease_id', $diseaseId);
              });
          })->count();

        // Count female patients with the specified disease related to the hospital
        $femalePatientsCount = $user->departments()
          ->whereHas('doctorsdepartment.patients', function ($query) use ($diseaseId) {
            $query->where('gender', 'female')
              ->whereHas('diseases', function ($query) use ($diseaseId) {
                $query->where('disease_id', $diseaseId);
              });
          })->count();
      }
    }

    $data = [
      'series' => [$malePatientsCount, $femalePatientsCount],
      'labels' => ['Male', 'Female']
    ];

    return response()->json($data);
  }

  public function patientBloodChart()
  {
    // Get the patient role
    $patientRole = Role::findByName('patient');

    // Initialize blood type counts
    $bloodTypesCount = [
      'A+' => 0,
      'A-' => 0,
      'B+' => 0,
      'B-' => 0,
      'AB+' => 0,
      'AB-' => 0,
      'O+' => 0,
      'O-' => 0,
    ];

    $user = Auth::user();
    $users = collect(); // Initialize an empty collection

    if ($user->hasRole('SuperAdmin')) {
      // Query users with the patient role and group by blood type
      $users = User::whereHas('roles', function ($query) use ($patientRole) {
        $query->where('role_id', $patientRole->id);
      })
        ->whereNotNull('blood_type')
        ->get()
        ->groupBy('blood_type');
    } elseif ($user->hasRole('doctor')) {
      // Query patients related to the doctor and group by blood type
      $users = $user->patients()
        ->whereNotNull('blood_type')
        ->get()
        ->groupBy('blood_type');
    } elseif ($user->hasRole('department')) {
      // Query patients related to the department and group by blood type
      $users = $user->doctorsdepartment()->with('patients')
        ->whereHas('patients', function ($query) {
          $query->whereNotNull('blood_type');
        })
        ->get()
        ->pluck('patients')
        ->flatten()
        ->groupBy('blood_type');
    } elseif ($user->hasRole('hospital')) {
      // Query patients related to the hospital and group by blood type
      $users = $user->departments()->with('doctorsdepartment.patients')
        ->whereHas('doctorsdepartment.patients', function ($query) {
          $query->whereNotNull('blood_type');
        })
        ->get()
        ->pluck('doctorsdepartment')
        ->flatten()
        ->pluck('patients')
        ->flatten()
        ->groupBy('blood_type');
    }

    // Map the results to bloodTypesCount array
    foreach ($users as $bloodType => $group) {
      $bloodTypesCount[$bloodType] = $group->count();
    }

    // Prepare data for the chart
    $data = [
      'series' => array_values($bloodTypesCount),
      'labels' => array_keys($bloodTypesCount),
    ];

    return response()->json($data);
  }



  public function getPatientCountsByDisease()
  {
    try {
      $user = Auth::user();
      $data = collect(); // Initialize an empty collection

      if ($user->hasRole('SuperAdmin')) {
        // Query to get disease names and count of patients per disease, limited to top twenty with most patients
        $data = Disease::withCount('patients')
          ->orderByDesc('patients_count') // Order by patient count in descending order
          ->take(20) // Limit to the first twenty diseases
          ->get();
      } elseif ($user->hasRole('doctor')) {
        // Query diseases with only patients related to the doctor
        $data = Disease::whereHas('patients', function ($query) use ($user) {
          $query->whereHas('doctors', function ($q) use ($user) {
            $q->where('doctor_id', $user->id);
          });
        })
          ->withCount(['patients' => function ($query) use ($user) {
            $query->whereHas('doctors', function ($q) use ($user) {
              $q->where('doctor_id', $user->id);
            });
          }])
          ->orderByDesc('patients_count')
          ->take(20)
          ->get();
      } elseif ($user->hasRole('department')) {
        // Query diseases with only patients related to the department
        $doctorIds = $user->doctorsdepartment()->pluck('id');
        $data = Disease::whereHas('patients', function ($query) use ($doctorIds) {
          $query->whereHas('doctors', function ($q) use ($doctorIds) {
            $q->whereIn('doctor_id', $doctorIds);
          });
        })
          ->withCount(['patients' => function ($query) use ($doctorIds) {
            $query->whereHas('doctors', function ($q) use ($doctorIds) {
              $q->whereIn('doctor_id', $doctorIds);
            });
          }])
          ->orderByDesc('patients_count')
          ->take(20)
          ->get();
      } elseif ($user->hasRole('hospital')) {
        // Query diseases with only patients related to the hospital
        $departmentIds = $user->departments()->pluck('id');
        $doctorIds = User::whereIn('department_id', $departmentIds)->pluck('id');
        $data = Disease::whereHas('patients', function ($query) use ($doctorIds) {
          $query->whereHas('doctors', function ($q) use ($doctorIds) {
            $q->whereIn('doctor_id', $doctorIds);
          });
        })
          ->withCount(['patients' => function ($query) use ($doctorIds) {
            $query->whereHas('doctors', function ($q) use ($doctorIds) {
              $q->whereIn('doctor_id', $doctorIds);
            });
          }])
          ->orderByDesc('patients_count')
          ->take(20)
          ->get();
      }

      return response()->json($data);
    } catch (\Exception $e) {
      // Handle any errors, log them, etc.
      return response()->json(['error' => 'Error fetching data'], 500);
    }
  }


  public function getReservationsData(Request $request)
  {
    try {
      $user = Auth::user();
      $data = [];

      if ($user->hasRole('SuperAdmin')) {
        if ($request->filter === 'by_month') {
          $data = DB::table('reservations')
            ->select(
              DB::raw('DATE_FORMAT(start_datetime, "%m/%y") as month'),
              DB::raw('COUNT(*) as total_reservations'),
              'label'
            )
            ->groupBy(DB::raw('DATE_FORMAT(start_datetime, "%m/%y")'), 'label')
            ->orderBy(DB::raw('DATE_FORMAT(start_datetime, "%m/%y")'))
            ->get();
        } elseif ($request->filter === 'last_30_days') {
          $data = DB::table('reservations')
            ->select(
              DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d") as day'),
              DB::raw('COUNT(*) as total_reservations'),
              'label'
            )
            ->where('start_datetime', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d")'), 'label')
            ->orderBy(DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d")'))
            ->get();
        } elseif ($request->filter === 'last_7_days') {
          $data = DB::table('reservations')
            ->select(
              DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d") as day'),
              DB::raw('COUNT(*) as total_reservations'),
              'label'
            )
            ->where('start_datetime', '>=', now()->subDays(7))
            ->groupBy(DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d")'), 'label')
            ->orderBy(DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d")'))
            ->get();
        } elseif ($request->filter === 'by_year') {
          $data = DB::table('reservations')
            ->select(
              DB::raw('YEAR(start_datetime) as year'),
              DB::raw('COUNT(*) as total_reservations'),
              'label'
            )
            ->groupBy(DB::raw('YEAR(start_datetime)'), 'label')
            ->orderBy(DB::raw('YEAR(start_datetime)'))
            ->get();
        } else {
          return response()->json(['error' => 'Invalid filter'], 400);
        }
      } elseif ($user->hasRole('doctor')) {
        if ($request->filter === 'by_month') {
          $data = DB::table('reservations')
            ->select(
              DB::raw('DATE_FORMAT(start_datetime, "%m/%y") as month'),
              DB::raw('COUNT(*) as total_reservations'),
              'label'
            )
            ->where('doctor_id', $user->id)
            ->groupBy(DB::raw('DATE_FORMAT(start_datetime, "%m/%y")'), 'label')
            ->orderBy(DB::raw('DATE_FORMAT(start_datetime, "%m/%y")'))
            ->get();
        } elseif ($request->filter === 'last_30_days') {
          $data = DB::table('reservations')
            ->select(
              DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d") as day'),
              DB::raw('COUNT(*) as total_reservations'),
              'label'
            )
            ->where('doctor_id', $user->id)
            ->where('start_datetime', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d")'), 'label')
            ->orderBy(DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d")'))
            ->get();
        } elseif ($request->filter === 'last_7_days') {
          $data = DB::table('reservations')
            ->select(
              DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d") as day'),
              DB::raw('COUNT(*) as total_reservations'),
              'label'
            )
            ->where('doctor_id', $user->id)
            ->where('start_datetime', '>=', now()->subDays(7))
            ->groupBy(DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d")'), 'label')
            ->orderBy(DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d")'))
            ->get();
        } elseif ($request->filter === 'by_year') {
          $data = DB::table('reservations')
            ->select(
              DB::raw('YEAR(start_datetime) as year'),
              DB::raw('COUNT(*) as total_reservations'),
              'label'
            )
            ->where('doctor_id', $user->id)
            ->groupBy(DB::raw('YEAR(start_datetime)'), 'label')
            ->orderBy(DB::raw('YEAR(start_datetime)'))
            ->get();
        } else {
          return response()->json(['error' => 'Invalid filter'], 400);
        }
      } elseif ($user->hasRole('department')) {
        if ($request->filter === 'by_month') {
          $departmentDoctorIds = $user->doctorsdepartment()->pluck('id');
          $data = Reservation::whereIn('doctor_id', $departmentDoctorIds)
            ->select(
              DB::raw('DATE_FORMAT(start_datetime, "%m/%y") as month'),
              DB::raw('COUNT(*) as total_reservations'),
              'label'
            )
            ->groupBy(DB::raw('DATE_FORMAT(start_datetime, "%m/%y")'), 'label')
            ->orderBy(DB::raw('DATE_FORMAT(start_datetime, "%m/%y")'))
            ->get();
        } elseif ($request->filter === 'last_30_days') {
          $departmentDoctorIds = $user->doctorsdepartment()->pluck('id');
          $data = Reservation::whereIn('doctor_id', $departmentDoctorIds)
            ->select(
              DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d") as day'),
              DB::raw('COUNT(*) as total_reservations'),
              'label'
            )
            ->where('start_datetime', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d")'), 'label')
            ->orderBy(DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d")'))
            ->get();
        } elseif ($request->filter === 'last_7_days') {
          $departmentDoctorIds = $user->doctorsdepartment()->pluck('id');
          $data = Reservation::whereIn('doctor_id', $departmentDoctorIds)
            ->select(
              DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d") as day'),
              DB::raw('COUNT(*) as total_reservations'),
              'label'
            )
            ->where('start_datetime', '>=', now()->subDays(7))
            ->groupBy(DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d")'), 'label')
            ->orderBy(DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d")'))
            ->get();
        } elseif ($request->filter === 'by_year') {
          $departmentDoctorIds = $user->doctorsdepartment()->pluck('id');
          $data = Reservation::whereIn('doctor_id', $departmentDoctorIds)
            ->select(
              DB::raw('YEAR(start_datetime) as year'),
              DB::raw('COUNT(*) as total_reservations'),
              'label'
            )
            ->groupBy(DB::raw('YEAR(start_datetime)'), 'label')
            ->orderBy(DB::raw('YEAR(start_datetime)'))
            ->get();
        } else {
          return response()->json(['error' => 'Invalid filter'], 400);
        }
      } elseif ($user->hasRole('hospital')) {
        if ($request->filter === 'by_month') {
          $departmentIds = $user->departments()->pluck('id');
          $hospitalDoctorIds = User::whereIn('department_id', $departmentIds)->pluck('id');
          $data = Reservation::whereIn('doctor_id', $hospitalDoctorIds)
            ->select(
              DB::raw('DATE_FORMAT(start_datetime, "%m/%y") as month'),
              DB::raw('COUNT(*) as total_reservations'),
              'label'
            )
            ->groupBy(DB::raw('DATE_FORMAT(start_datetime, "%m/%y")'), 'label')
            ->orderBy(DB::raw('DATE_FORMAT(start_datetime, "%m/%y")'))
            ->get();
        } elseif ($request->filter === 'last_30_days') {
          $departmentIds = $user->departments()->pluck('id');
          $hospitalDoctorIds = User::whereIn('department_id', $departmentIds)->pluck('id');
          $data = Reservation::whereIn('doctor_id', $hospitalDoctorIds)
            ->select(
              DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d") as day'),
              DB::raw('COUNT(*) as total_reservations'),
              'label'
            )
            ->where('start_datetime', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d")'), 'label')
            ->orderBy(DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d")'))
            ->get();
        } elseif ($request->filter === 'last_7_days') {
          $departmentIds = $user->departments()->pluck('id');
          $hospitalDoctorIds = User::whereIn('department_id', $departmentIds)->pluck('id');
          $data = Reservation::whereIn('doctor_id', $hospitalDoctorIds)
            ->select(
              DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d") as day'),
              DB::raw('COUNT(*) as total_reservations'),
              'label'
            )
            ->where('start_datetime', '>=', now()->subDays(7))
            ->groupBy(DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d")'), 'label')
            ->orderBy(DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d")'))
            ->get();
        } elseif ($request->filter === 'by_year') {
          $departmentIds = $user->departments()->pluck('id');
          $hospitalDoctorIds = User::whereIn('department_id', $departmentIds)->pluck('id');
          $data = Reservation::whereIn('doctor_id', $hospitalDoctorIds)
            ->select(
              DB::raw('YEAR(start_datetime) as year'),
              DB::raw('COUNT(*) as total_reservations'),
              'label'
            )
            ->groupBy(DB::raw('YEAR(start_datetime)'), 'label')
            ->orderBy(DB::raw('YEAR(start_datetime)'))
            ->get();
        } else {
          return response()->json(['error' => 'Invalid filter'], 400);
        }
      }

      return response()->json($data);
    } catch (\Exception $e) {
      Log::error('Error fetching reservations data: ' . $e->getMessage());
      return response()->json(['error' => 'Error fetching data'], 500);
    }
  }
}
