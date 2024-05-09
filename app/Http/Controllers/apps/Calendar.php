<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;

class Calendar extends Controller
{
  public function index()
  {
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
    return view('content.apps.app-calendar', compact('patients', 'doctors'));
  }


  public function getEvents(Request $request)
  {
    // Fetch events from the Reservation model
    $reservations = Reservation::query();

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
    $reservation->doctor_id = $request->eventDoctors;
    $reservation->patient_id = $request->eventPatients;
    $reservation->description = $request->eventDescription;
    $reservation->save();

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

    // Update Reservation instance with new data
    $reservation->name = $request->eventTitle;
    $reservation->start_datetime = $request->eventStartDate;
    $reservation->end_datetime = $request->eventEndDate;
    $reservation->label = $request->eventLabel;
    $reservation->doctor_id = $request->eventDoctors;
    $reservation->patient_id = $request->eventPatients;
    $reservation->description = $request->eventDescription;
    $reservation->save();

    // Return a response indicating success
    return response()->json(['message' => 'updated']);
  }
  public function destroy($id)
  {

    Reservation::where('id', $id)->delete();
  }
}
