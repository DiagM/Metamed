<?php

namespace App\Http\Controllers;

use App\Jobs\SendHolidayEmail;
use App\Models\Holiday;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HolidayController extends Controller
{
  public function index(Request $request)
  {
    $columns = [
      1 => 'holidays.id',
      2 => 'holidays.reason',
      3 => 'holidays.date_start',
      4 => 'holidays.date_end',
      5 => 'holidays.status',
      6 => 'doctors.name', // Assuming 'doctors' is the relationship method name in Holiday model for doctor
      7 => 'departments.name', // Assuming 'departments' is the relationship method name in Holiday model for department
    ];

    $search = $request->input('search.value');
    $totalData = Holiday::count();
    $totalFiltered = $totalData;
    $limit = $request->input('length');
    $start = $request->input('start');
    $orderColumn = $request->input('order.0.column');
    $order = $columns[$orderColumn];
    $dir = $request->input('order.0.dir');

    $query = Holiday::with(['doctor', 'department']);

    // Check the authenticated user's role
    if (auth()->user()->hasRole('doctor')) {
      $query->where('doctor_id', auth()->id());
    } elseif (auth()->user()->hasRole('department')) {
      $query->where('department_id', auth()->id());
    }

    if (!empty($search)) {
      $query->where(function ($q) use ($search) {
        $q->where('holidays.id', 'LIKE', "%{$search}%")
          ->orWhere('holidays.reason', 'LIKE', "%{$search}%")
          ->orWhere('holidays.date_start', 'LIKE', "%{$search}%")
          ->orWhere('holidays.date_end', 'LIKE', "%{$search}%")
          ->orWhere('holidays.status', 'LIKE', "%{$search}%")
          ->orWhereHas('doctor', function ($query) use ($search) {
            $query->where('users.name', 'LIKE', "%{$search}%");
          })
          ->orWhereHas('department', function ($query) use ($search) {
            $query->where('users.name', 'LIKE', "%{$search}%");
          });
      });

      $totalFiltered = $query->count();
    }

    $holidays = $query->offset($start)
      ->limit($limit)
      ->orderBy($order, $dir)
      ->get();

    $data = [];
    foreach ($holidays as $index => $holiday) {
      $nestedData['id'] = $holiday->id;
      $nestedData['fake_id'] = $start + $index + 1;
      $nestedData['reason'] = $holiday->reason;
      $nestedData['date_start'] = $holiday->date_start;
      $nestedData['date_end'] = $holiday->date_end;
      $nestedData['status'] = $holiday->status;
      $nestedData['doctor_name'] = $holiday->doctor ? $holiday->doctor->name : 'N/A';
      $nestedData['department_name'] = $holiday->department ? $holiday->department->name : 'N/A';

      $data[] = $nestedData;
    }

    return response()->json([
      'draw' => intval($request->input('draw')),
      'recordsTotal' => intval($totalData),
      'recordsFiltered' => intval($totalFiltered),
      'data' => $data,
    ]);
  }

  public function store(Request $request)
  {
    // Validate the request data
    $validatedData = $request->validate([
      'id' => 'nullable|exists:holidays,id',
      'reason' => 'required|string|max:255',
      'start_date' => 'required|date',
      'end_date' => 'required|date|after_or_equal:start_date',
    ]);
    $doctor = Auth::user();
    $department = $doctor->department;

    // Create new holiday
    $holiday = new Holiday;
    $holiday->reason = $request->input('reason');
    $holiday->date_start = $request->input('start_date');
    $holiday->date_end = $request->input('end_date');
    $holiday->status = 'pending';
    $holiday->doctor_id = $doctor->id;
    $holiday->department_id = $department->id;
    $holiday->save();

    $status = 'created';


    // Return success response
    return response()->json($status);
  }

  public function changestatus(Request $request)
  {
    // Validate the incoming request
    $request->validate([
      'status' => 'required'
    ]);

    try {
      // Find the holiday by ID
      $holiday = Holiday::findOrFail($request->id);

      // Update the holiday with new values
      $holiday->status = $request->input('status');

      // Save the updated holiday
      $holiday->save();
      if ($holiday->status == 'confirmed') {
        $doctor = $holiday->doctor;
        $department = $holiday->department;
        $reason = $holiday->reason;
        $start_date = $holiday->date_start;
        $end_date = $holiday->date_end;
        $currentDate = Carbon::now()->format('Y-m-d');
        $data = [
          'doctorName' => $doctor->name,
          'departmentName' => $department->name,
          'doctorHospital' => $department->hospital->name,
          'currentDate' => $currentDate,
          'reason' => $reason,
          'start_date' => $start_date,
          'end_date' => $end_date
        ];
        // Generate and download the PDF
        $pdf = Pdf::loadView('content.laravel-example.holiday', $data);
        // Convert PDF to base64 encoded string
        $pdfContent = base64_encode($pdf->output());
        $fileName = "holiday_{$doctor->name}_{$currentDate}.pdf";
        // Dispatch job to send email with attached file
        SendHolidayEmail::dispatch($doctor->name, $doctor->email, $pdfContent, $fileName);
        return $pdf->download($fileName);
      }

      return response()->json(['status' => 'updated'], 200);
    } catch (\Exception $e) {
      return response()->json(['error' => 'Failed to update holiday'], 500);
    }
  }
}
