<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use RTippin\Messenger\Facades\Messenger;

class UsersTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    //ADMIN
    $user = User::create([
      'name' => 'Admin User',
      'email' => 'admin@admin.com',
      'password' => bcrypt("password")
    ]);
    $user->assignRole('SuperAdmin');
    //HOSPITAL
    $hospital = User::create([
      'name' => 'Hospital',
      'email' => 'musatapha@hospital.com',
      'contact' => '055616',
      'password' => bcrypt("password")
    ]);
    $hospital->assignRole('hospital');
    //DEPARTMENT
    $department1 = User::create([
      'name' => 'cardiology',
      'email' => 'cardiology@mustapha.com',
      'contact' => '055616',
      'hospital_id' => $hospital->id,
      'password' => bcrypt("password")
    ]);
    $department1->assignRole('department');
    $department2 = User::create([
      'name' => 'Neurology',
      'email' => 'Neurology@mustapha.com',
      'contact' => '055616',
      'hospital_id' => $hospital->id,
      'password' => bcrypt("password")
    ]);
    $department2->assignRole('department');
    //DOCTOR
    $doctor1 = User::create([
      'name' => 'doctor1',
      'email' => 'doctor.cardiology@mustapha.com',
      'contact' => '055616',
      'license_number' => 'A1',
      'department_id' => $department1->id,
      'password' => bcrypt("password")
    ]);
    $doctor2 = User::create([
      'name' => 'doctor2',
      'email' => 'doctor.Neurology@mustapha.com',
      'contact' => '055616',
      'license_number' => 'A2',
      'department_id' => $department2->id,
      'password' => bcrypt("password")
    ]);
    $doctor1->assignRole('doctor');
    $doctor2->assignRole('doctor');
    Messenger::getProviderMessenger($user);
  }
}
