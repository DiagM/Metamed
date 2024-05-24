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
use RTippin\Messenger\Contracts\MessengerProvider;
use RTippin\Messenger\Traits\Messageable;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable implements CanResetPassword, MessengerProvider
{
  use HasApiTokens, HasFactory, Notifiable,  HasRoles, Messageable;

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

  public static function getProviderSettings(): array
  {
    return [
      'alias' => 'user',
      'searchable' => true,
      'friendable' => true,
      'devices' => true,
      'default_avatar' => public_path('vendor/messenger/images/users.png'),
      'cant_message_first' => [],
      'cant_search' => [],
      'cant_friend' => [],
    ];
  }
  public static function getProviderSearchableBuilder(
    Builder $query,
    string $search,
    array $searchItems
  ) {
    $query->where(function (Builder $query) use ($searchItems) {
      foreach ($searchItems as $item) {
        $query->orWhere('name', 'LIKE', "%{$item}%")
          ->orWhere('license_number', 'LIKE', "%{$item}%");
      }
    })->orWhere('email', '=', $search);
  }

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
