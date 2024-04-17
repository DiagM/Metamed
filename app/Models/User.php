<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements CanResetPassword
{
  use HasApiTokens, HasFactory, Notifiable,  HasRoles;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
    'hospital_id',
    'department_id',
    'contact',
    'license_number',
    'date_of_birth',
    'address',
    'gender',
    'latitude',
    'longitude'
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
  ];

  public function medicalFiles()
  {
    return $this->hasMany(MedicalFile::class);
  }
  public function doctorReservations()
  {
    return $this->hasMany(Reservation::class, 'doctor_id');
  }

  public function patientReservations()
  {
    return $this->hasMany(Reservation::class, 'patient_id');
  }
  public function hospital()
  {
    return $this->belongsTo(User::class);
  }
  public function department()
  {
    return $this->belongsTo(User::class);
  }
  public function hospitals()
  {
    return $this->hasMany(User::class, 'hospital_id');
  }

  public function departments()
  {
    return $this->hasMany(User::class, 'department_id');
  }
  // Define the many-to-many relationship with itself for doctors
  public function doctors(): BelongsToMany
  {
    return $this->belongsToMany(User::class, 'patient_doctor', 'patient_id', 'doctor_id');
  }

  // Define the many-to-many relationship with itself for patients
  public function patients(): BelongsToMany
  {
    return $this->belongsToMany(User::class, 'patient_doctor', 'doctor_id', 'patient_id');
  }
}
