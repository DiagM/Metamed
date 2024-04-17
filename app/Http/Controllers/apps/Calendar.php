<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

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
    $reservations = Reservation::all();

    // Prepare the events data in the format expected by the front end
    $events = $reservations->map(function ($reservation) {
      return [
        'id' => $reservation->id,
        'title' => $reservation->description,
        'start' => $reservation->start_datetime,
        'end' => $reservation->end_datetime,
        'extendedProps' => [
          'calendar' => $reservation->name
        ],
      ];
    });

    // Return the events data as JSON response
    return response()->json($events);
  }
}
