<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disease extends Model
{
  use HasFactory;
  protected $fillable = [
    'name',
  ];

  public function patients()
  {
    return $this->belongsToMany(User::class, 'disease_user', 'disease_id', 'patient_id');
  }
}
