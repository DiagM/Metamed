<?php

namespace App\Http\Controllers\laravel_example;

use App\Http\Controllers\Controller;
use App\Jobs\SendPrescriptionEmail;
use App\Models\MedicalFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MedicalFileManagement extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $columns = [
      1 => 'medical_files.id',
      2 => 'medical_files.file_name',
      3 => 'medical_files.description',
      4 => 'medical_files.created_at',
      5 => 'users.name',
    ];

    $search = $request->input('search.value');
    $totalData = MedicalFile::join('users', 'medical_files.doctor_id', '=', 'users.id')->count();
    $totalFiltered = $totalData;
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');

    if (empty($search)) {
      $files = MedicalFile::with('doctor')
        ->join('users', 'medical_files.doctor_id', '=', 'users.id')
        ->select('medical_files.*', 'users.name as doctor_name')
        ->offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();
    } else {
      $files = MedicalFile::with('doctor')
        ->join('users', 'medical_files.doctor_id', '=', 'users.id')
        ->where(function ($query) use ($search) {
          $query->where('medical_files.id', 'LIKE', "%{$search}%")
            ->orWhere('medical_files.file_name', 'LIKE', "%{$search}%")
            ->orWhere('medical_files.created_at', 'LIKE', "%{$search}%")
            ->orWhere('users.name', 'LIKE', "%{$search}%");
        })
        ->select('medical_files.*', 'users.name as doctor_name')
        ->offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();

      $totalFiltered = MedicalFile::join('users', 'medical_files.doctor_id', '=', 'users.id')
        ->where(function ($query) use ($search) {
          $query->where('medical_files.id', 'LIKE', "%{$search}%")
            ->orWhere('medical_files.file_name', 'LIKE', "%{$search}%")
            ->orWhere('medical_files.created_at', 'LIKE', "%{$search}%")
            ->orWhere('users.name', 'LIKE', "%{$search}%");
        })
        ->count();
    }

    $data = [];
    if (!empty($files)) {
      $ids = $start;
      foreach ($files as $file) {
        $nestedData['id'] = $file->id;
        $nestedData['fake_id'] = ++$ids;
        $nestedData['file_name'] = $file->file_name;
        $nestedData['description'] = $file->description;
        $nestedData['created_at'] = $file->created_at->format('Y-m-d');
        $nestedData['file_path'] = $file->file_path;
        $nestedData['doctor_name'] = $file->doctor_name ?: 'N/A';

        $data[] = $nestedData;
      }
    }

    return response()->json([
      'draw' => intval($request->input('draw')),
      'recordsTotal' => intval($totalData),
      'recordsFiltered' => intval($totalFiltered),
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
    // Validate the incoming request data
    $request->validate([
      'file_name' => 'required|string',
      'description' => 'required|string',
      'medical_file' => 'required|file', // Validate the file upload
    ]);
    $medicalfileID = $request->id;

    if ($medicalfileID) {
      // update the value
      // Retrieve the old file path
      $oldMedicalFile = MedicalFile::find($medicalfileID);
      $oldFilePath = $oldMedicalFile->file_path;
      // Delete the old file
      if ($oldFilePath && Storage::exists($oldFilePath)) {
        Storage::delete($oldFilePath);
      }
      // Handle file upload
      if ($request->hasFile('medical_file')) {
        // Get the uploaded file
        $file = $request->file('medical_file');

        // Generate a custom filename based on the original filename
        $customFileName = $request->file_name . '.' . $file->getClientOriginalExtension();

        // Store the file with the custom filename and get its path
        $filePath = $file->storeAs('medical_files', $customFileName);
      }
      $medicalfileID = MedicalFile::updateOrCreate(
        ['id' => $medicalfileID],
        [
          'file_name' => $request->file_name, 'description' => $request->description, 'patient_id' => $request->patient_id,
          'file_path' => $filePath, 'doctor_id' => Auth::id()
        ]
      );

      // user updated
      return response()->json('Updated');
    } else {

      // Handle file upload
      if ($request->hasFile('medical_file')) {
        // Get the uploaded file
        $file = $request->file('medical_file');

        // Generate a custom filename based on the original filename
        $customFileName = $request->file_name . '.' . $file->getClientOriginalExtension();

        // Store the file with the custom filename and get its path
        $filePath = $file->storeAs('medical_files', $customFileName);
      }

      // Create a new MedicalFile instance
      $medicalFile = new MedicalFile();
      $medicalFile->file_name = $request->file_name;
      $medicalFile->description = $request->description;
      $medicalFile->patient_id = $request->patient_id;
      $medicalFile->doctor_id = Auth::id();
      $medicalFile->file_path = $filePath; // Store the file path
      // Save the MedicalFile instance to the database
      $medicalFile->save();

      // Return a response indicating success
      return response()->json('Created');
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

    $medicalFile = MedicalFile::where($where)->first();

    return response()->json($medicalFile);
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

    // Retrieve the old file path
    $oldMedicalFile = MedicalFile::find($id);
    $oldFilePath = $oldMedicalFile->file_path;
    // Delete the old file
    if ($oldFilePath && Storage::exists($oldFilePath)) {
      Storage::delete($oldFilePath);
    }
    $medicalFile = MedicalFile::where('id', $id)->delete();
  }


  public function prescription(Request $request)
  {
    // Get the patient and doctor information
    $patient = User::where('id', $request->input('prescriptionPatientId'))->first();
    $doctor = User::where('id', Auth::id())->first();

    // Calculate the patient's age
    $patientDateOfBirth = $patient->date_of_birth;
    $patientAge = Carbon::parse($patientDateOfBirth)->age;

    // Process form data
    $patientName = $request->input('prescriptionPatientName');
    $patientEmail = $patient->email;
    $medications = $request->input('prescriptionMedication');
    $dosages = $request->input('prescriptionDosage');
    $instructions = $request->input('prescriptionInstructions');

    // Get today's date
    $currentDate = Carbon::now()->format('Y-m-d');

    // Prepare data for the view
    $data = [
      'patientName' => $patientName,
      'patientAge' => $patientAge,
      'doctorName' => $doctor->name,
      'doctorDepartment' => $doctor->department->name,
      'doctorHospital' => $doctor->department->hospital->name,
      'currentDate' => $currentDate,
      'medications' => $medications,
      'dosages' => $dosages,
      'instructions' => $instructions
    ];

    // Generate and download the PDF
    $pdf = Pdf::loadView('content.laravel-example.prescription', $data);
    // Convert PDF to base64 encoded string
    $pdfContent = base64_encode($pdf->output());
    $fileName = "prescription_{$patientName}_{$currentDate}.pdf";
    // Dispatch job to send email with attached file
    SendPrescriptionEmail::dispatch($patientName, $pdfContent, $patientEmail, $doctor->name, $fileName);


    return $pdf->download($fileName);
  }


  // public function downloadFile($id)
  // {
  //     // Find the file record in the database
  //     $fileRecord = MedicalFile::find($id);

  //     if (!$fileRecord) {
  //         return response()->json(['message' => 'File not found'], 404);
  //     }

  //     $filePath = $fileRecord->file_path;
  //     $file = storage_path('app/' . $filePath);

  //     if (!file_exists($file)) {
  //         return response()->json(['message' => 'File not found'], 404);
  //     }

  //     // Generate a public URL for the file
  //     $fileUrl = Storage::url($filePath); // Get the URL for the file

  //     // Determine the file type and name
  //     $fileType = mime_content_type($file);
  //     $fileName = basename($filePath);

  //     return response()->json([
  //         'file_url' => $fileUrl,
  //         'file_type' => $fileType,
  //         'file_name' => $fileName
  //     ], 200);
  // }


}
