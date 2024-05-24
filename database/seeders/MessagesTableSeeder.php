<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RTippin\Messenger\Models\Message;
use RTippin\Messenger\Models\Thread;
use Throwable;

class MessagesTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    DB::transaction(function () {
      // Seed ALL threads with messages
      Thread::with('participants.owner')->get()->each(function (Thread $thread) {
        for ($x = 0; $x < rand(5, 20); $x++) {
          Message::factory()
            ->for($thread)
            ->owner($thread->participants->random()->owner)
            ->create();
        }
      });
    });
  }
}
