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
    $user = User::create([
      'name' => 'Admin User',
      'email' => 'admin@admin.com',
      'password' => bcrypt("password")
    ]);
    $user->assignRole('SuperAdmin');
    Messenger::getProviderMessenger($user);
    $user1 = User::create([
      'name' => 'doctor',
      'email' => 'doctor@test.com',
      'license_number' => 'test',
      'password' => bcrypt("password")
    ]);
    $user1->assignRole('doctor');
    Messenger::getProviderMessenger($user1);

    $user2 = User::create([
      'name' => 'doctor2',
      'email' => 'doctor2@test.com',
      'license_number' => 'test2',
      'password' => bcrypt("password")
    ]);
    $user1->assignRole('doctor');
    Messenger::getProviderMessenger($user2);
  }
}
