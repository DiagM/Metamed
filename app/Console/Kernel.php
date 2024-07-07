<?php

namespace App\Console;

use App\Models\Reservation;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Carbon\Carbon;
use App\Notifications\ReservationReminderNotification;
use Illuminate\Support\Facades\Notification;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
          $targetDate = Carbon::now()->addDays(2)->toDateString();
          $reservations = Reservation::whereDate('start_datetime', $targetDate)->get();

          foreach ($reservations as $reservation) {
              $patient = $reservation->patient;

              if ($patient && $patient->expoTokens()->exists()) {
                $title = 'Reservation Reminder';
                $body = 'Your reservation '. $reservation->name .'  on ' . $reservation->start_datetime . ' is approaching.';
                Notification::send($patient, new ReservationReminderNotification($title, $body));
            }
          }
      })->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
