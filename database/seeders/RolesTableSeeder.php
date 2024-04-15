<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Create roles
    Role::create(['name' => 'SuperAdmin']);
    Role::create(['name' => 'hospitalmanage']);
    Role::create(['name' => 'hospital']);
    Role::create(['name' => 'departmentmanage']);
    Role::create(['name' => 'department']);
    Role::create(['name' => 'doctormanage']);
    Role::create(['name' => 'doctor']);
  }
}
