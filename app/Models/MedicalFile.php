<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalFile extends Model
{
  use HasFactory;
  protected $fillable = [
    'file_name',
    'description',
    'file_path',
    'patient_id',
    'doctor_id',
  ];

  public function patient()
  {
    return $this->belongsTo(User::class, 'patient_id');
  }
  public function doctor()
  {
    return $this->belongsTo(User::class, 'doctor_id');
  }
}
