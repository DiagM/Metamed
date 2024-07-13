<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
  use HasFactory;
  protected $fillable = [
    'reason',
    'date_start',
    'date_end',
    'status',
    'doctor_id',
    'department_id'
  ];


  public function department()
  {
    return $this->belongsTo(User::class, 'department_id');
  }
  public function doctor()
  {
    return $this->belongsTo(User::class, 'doctor_id');
  }
}
