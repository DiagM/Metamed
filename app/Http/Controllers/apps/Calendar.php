<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Notifications\ReservationReminderNotification;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class Calendar extends Controller
{
  public function index()
  {
    $user = Auth::user();
    if ($user->hasRole('SuperAdmin')) {
      // Get the "patient" role
      $patientRole = Role::findByName('patient');
      $doctorRole = Role::findByName('doctor');
      // Retrieve users with the "patient" role
      $patients = User::whereHas('roles', function ($query) use ($patientRole) {
        $query->where('role_id', $patientRole->id);
      })->get();
      $doctors = User::whereHas('roles', function ($query) use ($doctorRole) {
        $query->where('role_id', $doctorRole->id);
      })->get();
    } elseif ($user->hasRole('hospital')) {
      // Retrieve departments of the hospital
      $departments = $user->departments;


      // Collect all patients of departments under the hospital
      $patients = collect();
      $doctors = collect();
      foreach ($departments as $department) {
        $departmentDoctors = $department->doctorsdepartment;
        $doctors = $doctors->merge($departmentDoctors);
        foreach ($departmentDoctors as $doctor) {
          $patients = $patients->merge($doctor->patients);
        }
      }
    } elseif ($user->hasRole('department')) {
      // Retrieve doctors of the department
      $doctors = $user->doctorsdepartment;
      // Collect all patients of doctors in the department
      $patients = $doctors->pluck('patients')->flatten();
    } elseif (($user->hasRole('doctor'))) {
      $patients = $user->patients;
      $doctors = collect();
    }
    return view('content.apps.app-calendar', compact('patients', 'doctors', 'user'));
  }


  public function getEvents(Request $request)
  {
    $user = Auth::user();
    // Fetch events from the Reservation model
    if ($user->hasRole('doctor')) {
      $reservations = Reservation::query()
        ->where('doctor_id', '=', $user->id);
    } elseif ($user->hasRole('hospital')) {
      // For hospitals, get reservations through departments and doctors
      $reservations = Reservation::query()
        ->whereHas('doctor.department.hospital', function ($query) use ($user) {
          // Filter by hospital_id that the hospital user belongs to
          $query->where('id', '=', $user->id);
        });
    } elseif ($user->hasRole('department')) {
      // For hospitals, get reservations through departments and doctors
      $reservations = Reservation::query()
        ->whereHas('doctor.department', function ($query) use ($user) {
          // Filter by hospital_id that the hospital user belongs to
          $query->where('id', '=', $user->id);
        });
    } elseif ($user->hasRole('SuperAdmin')) {
      // For hospitals, get reservations through departments and doctors
      $reservations = Reservation::query();
    }

    // Check if filters are provided in the request
    if ($request->has('filters')) {
      // Get selected filters
      $filters = json_decode($request->input('filters'));

      // Filter reservations based on selected filters
      if (!empty($filters)) {
        $reservations->whereIn('label', $filters);
      }
    }

    // Check if patient IDs are provided in the request
    if ($request->has('patientIds')) {
      // Get selected patient IDs
      $patientIds = json_decode($request->input('patientIds'));

      // Filter reservations based on selected patient IDs
      if (!empty($patientIds)) {
        $reservations->whereIn('patient_id', $patientIds);
      }
    }
    if ($request->has('doctorsIds')) {
      // Get selected patient IDs
      $doctorIds = json_decode($request->input('doctorsIds'));

      // Filter reservations based on selected patient IDs
      if (!empty($doctorIds)) {
        $reservations->whereIn('doctor_id', $doctorIds);
      }
    }

    // Fetch filtered reservations
    $reservations = $reservations->get();

    // Prepare the events data in the format expected by the front end
    $events = $reservations->map(function ($reservation) {
      return [
        'id' => $reservation->id,
        'title' => $reservation->name,
        'start' => $reservation->start_datetime,
        'end' => $reservation->end_datetime,
        'extendedProps' => [
          'calendar' => $reservation->label,
          'doctor_id' => $reservation->doctor_id,
          'patient_id' => $reservation->patient_id,
          'description' => $reservation->description
        ],
      ];
    });

    // Return the events data as JSON response
    return response()->json($events);
  }


  public function addEvent(Request $request)
  {
    // Validate incoming request data
    $validator = Validator::make($request->all(), [
      'eventTitle' => 'required',
      'eventStartDate' => 'required|date',
      'eventEndDate' => 'required|date|after_or_equal:eventStartDate',
      'eventLabel' => 'required',
      'eventDoctors' => 'required|exists:users,id',
      'eventPatients' => 'required|exists:users,id',
      'eventDescription' => 'required',
    ], [
      'eventEndDate.after_or_equal' => 'The End Date must be after or equal to the Start Date.'
    ]);

    // Check if validation fails
    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    // Create a new Reservation instance
    $reservation = new Reservation();
    $reservation->name = $request->eventTitle;
    $reservation->start_datetime = $request->eventStartDate;
    $reservation->end_datetime = $request->eventEndDate;
    $reservation->label = $request->eventLabel;
    $reservation->doctor_id = Auth::id();
    $reservation->patient_id = $request->eventPatients;
    $reservation->description = $request->eventDescription;
    $reservation->save();




    $patient = $reservation->patient;

    if ($patient && $patient->expoTokens()->exists()) {
      $title = 'Reservation Created';
      $body = 'Your reservation ' . $reservation->name . '  on ' . $reservation->start_datetime . ' is created.';
      Notification::send($patient, new ReservationReminderNotification($title, $body));
    }


    // Return a response indicating success
    return response()->json(['message' => 'added']);
  }
  public function updateEvent(Request $request, $id)
  {

    // Validate incoming request data
    $validator = Validator::make($request->all(), [
      'eventTitle' => 'required',
      'eventStartDate' => 'required|date',
      'eventEndDate' => 'required|date|after_or_equal:eventStartDate',
      'eventLabel' => 'required',
      'eventDoctors' => 'required|exists:users,id',
      'eventPatients' => 'required|exists:users,id',
      'eventDescription' => 'required',
    ], [
      'eventEndDate.after_or_equal' => 'The End Date must be after or equal to the Start Date.'
    ]);

    // Check if validation fails
    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    // Find the Reservation instance by ID
    $reservation = Reservation::find($id);

    // Check if the reservation exists
    if (!$reservation) {
      return response()->json(['error' => 'Reservation not found'], 404);
    }
    $olddate = new DateTime($reservation->start_datetime);



    // Update Reservation instance with new data
    $reservation->name = $request->eventTitle;
    $reservation->start_datetime = $request->eventStartDate;
    $reservation->end_datetime = $request->eventEndDate;
    $reservation->label = $request->eventLabel;
    $reservation->doctor_id = Auth::id();
    $reservation->patient_id = $request->eventPatients;
    $reservation->description = $request->eventDescription;
    $reservation->save();

    $newDate = new DateTime($request->eventStartDate);

    if ($olddate != $newDate) {

      $patient = $reservation->patient;

      if ($patient && $patient->expoTokens()->exists()) {
        $title = 'Reservation Updated';
        $body = 'Your reservation ' . $reservation->name . '  has been rescheduled to ' . $reservation->start_datetime . '.';
        Notification::send($patient, new ReservationReminderNotification($title, $body));
      }
    }
    // Return a response indicating success
    return response()->json(['message' => 'updated']);
  }
  public function destroy($id)
  {
    $reservation = Reservation::where('id', $id)->first();
    $patient = $reservation->patient;

    if ($patient && $patient->expoTokens()->exists()) {
      $title = 'Reservation Cancel';
      $body = 'Your reservation ' . $reservation->name . '  of date ' . $reservation->start_datetime . 'at the doctor' . $reservation->doctor->name . 'has been canceled.';
      Notification::send($patient, new ReservationReminderNotification($title, $body));
    }
    Reservation::where('id', $id)->delete();
  }
  public function indexmobile()
  {
    $userId = Auth::id();
    $reservations = Reservation::with(['doctor.department.hospital', 'patient'])
      ->where('patient_id', $userId)
      ->orderBy('start_datetime', 'desc')
      ->get();
    return response()->json($reservations);
  }
}
