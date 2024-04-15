<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{

  /**
   * Seed the application's database.
   */
  public function run(): void
  {

    // Call the RoleSeeder
    $this->call(RolesTableSeeder::class);

    $user = User::create([
      'name' => 'Admin User',
      'email' => 'admin@admin.com',
      'password' => bcrypt("adminadmin")
    ]);
    $user->assignRole('SuperAdmin');
  }
}
