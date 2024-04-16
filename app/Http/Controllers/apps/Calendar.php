<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class Calendar extends Controller
{
  public function index()
  {
    return view('content.apps.app-calendar');
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
