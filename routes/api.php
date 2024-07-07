<?php

use App\Http\Controllers\apps\Calendar;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpoTokenController;
use App\Http\Controllers\laravel_example\MedicalFileManagement;
use App\Http\Controllers\laravel_example\PatientManagement;
use App\Http\Controllers\laravel_example\UserManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});
Route::get('/usermobile', [UserManagement::class, 'indexmobile']);
Route::middleware(['auth:sanctum'])->group(function () {
  Route::post('logout', [AuthController::class, 'logout']);
  Route::get('/user/roles', [UserManagement::class, 'userroles']);
  //patient
  Route::get('/patient/files', [PatientManagement::class, 'patientfiles']);
  Route::get('/patient/reservation', [PatientManagement::class, 'patientreservation']);
  //pushNotification
  Route::post('save-push-token', [ExpoTokenController::class, 'store']);
  Route::post('delete-push-token', [ExpoTokenController::class, 'delete']);
  //reservation
  Route::get('/reservations', [Calendar::class, 'indexmobile']);

});
Route::get('/download', function (Request $request) {

  $filePath = $request->input('url');
  return response()->download(storage_path('app/' . $filePath));
});
