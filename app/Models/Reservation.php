<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'doctor_id',
    'patient_id',
    'start_datetime',
    'end_datetime',
    'description',
  ];

  public function doctor()
  {
    return $this->belongsTo(User::class);
  }

  public function patient()
  {
    return $this->belongsTo(User::class);
  }
}
