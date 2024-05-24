<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RTippin\Messenger\Models\Participant;
use RTippin\Messenger\Models\Thread;
use Throwable;

class ThreadsTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   *
   * @throws Throwable
   */
  public function run()
  {
    $users = User::all();

    // Create private threads between users
    $this->makePrivates($users);
  }

  private function makePrivates(Collection $users): void
  {
    foreach ($users as $user) {
      // Filter out the user itself from the collection
      $others = $users->where('id', '!=', $user->id);

      foreach ($others as $other) {
        // Check if a private thread doesn't exist between these two users
        if (!Thread::hasProvider($user)
          ->where(function ($query) use ($other) {
            $query->whereHas('participants', function ($query) use ($other) {
              $query->where('owner_id', $other->id)
                ->where('owner_type', get_class($other))
                ->whereNull('deleted_at');
            });
          })
          ->private()
          ->exists()) {
          // Create a new private thread
          $private = Thread::factory()->create();

          // Add participants to the thread
          Participant::factory()->for($private)->owner($user)->read()->create();
          Participant::factory()->for($private)->owner($other)->read()->create();
        }
      }
    }
  }
}
