<?php

namespace App\Http\Controllers\laravel_example;

use App\Http\Controllers\Controller;
use App\Models\MedicalFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MedicalFileManagement extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $columns = [
      1 => 'id',
      2 => 'file_name',
      3 => 'description',
      4 => 'created_at',


    ];

    $search = [];

    $totalData = MedicalFile::count();

    $totalFiltered = $totalData;

    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');

    if (empty($request->input('search.value'))) {
      $users = MedicalFile::offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();
    } else {
      $search = $request->input('search.value');

      $users = MedicalFile::where('id', 'LIKE', "%{$search}%")
        ->orWhere('file_name', 'LIKE', "%{$search}%")
        ->orWhere('created_at', 'LIKE', "%{$search}%")
        ->offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();

      $totalFiltered = MedicalFile::where('id', 'LIKE', "%{$search}%")
        ->orWhere('file_name', 'LIKE', "%{$search}%")
        ->orWhere('created_at', 'LIKE', "%{$search}%")
        ->count();
    }

    $data = [];

    if (!empty($users)) {
      // providing a dummy id instead of database ids
      $ids = $start;

      foreach ($users as $user) {
        $nestedData['id'] = $user->id;
        $nestedData['fake_id'] = ++$ids;
        $nestedData['file_name'] = $user->file_name;
        $nestedData['description'] = $user->description;
        $nestedData['created_at'] = $user->created_at;
        $nestedData['file_path'] = $user->file_path;



        $data[] = $nestedData;
      }
    }

    if ($data) {
      return response()->json([
        'draw' => intval($request->input('draw')),
        'recordsTotal' => intval($totalData),
        'recordsFiltered' => intval($totalFiltered),
        'code' => 200,
        'data' => $data,
      ]);
    } else {
      return response()->json([
        'message' => 'Internal Server Error',
        'code' => 500,
        'data' => [],
      ]);
    }
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
          'file_path' => $filePath,
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
}
