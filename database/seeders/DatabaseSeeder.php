<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use RTippin\Messenger\Facades\Messenger;
use Illuminate\Support\Facades\DB;
use RTippin\Messenger\Models\Message;
use RTippin\Messenger\Models\Thread;
use Throwable;

class DatabaseSeeder extends Seeder
{
  const Admin = [
    'name' => 'John Doe',
    'email' => 'admin@example.net',
  ];

  /**
   * Seed the application's database.
   */
  public function run(): void
  {

    // Call the RoleSeeder
    $this->call([RolesTableSeeder::class, UsersTableSeeder::class, ThreadsTableSeeder::class,  MessagesTableSeeder::class]);
  }
}
