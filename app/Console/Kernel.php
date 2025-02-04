<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Card;
use App\Notifications\TaskNotification;
use App\Models\User;

class Kernel extends ConsoleKernel
{
    // ...existing code...

    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $overdueTasks = Card::where('due_date', '<', now())->get();
            // dd($overdueTasks); // Uncomment for debugging
            foreach ($overdueTasks as $task) {
                $user = User::find($task->user_project_id);
                if ($user) {
                    $user->notify(new TaskNotification($user, $task));
                }
            }
        })->hourly(); // Revert the frequency after testing
    }

    // ...existing code...
}
