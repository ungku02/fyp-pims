<?php

namespace App\Listeners;

use App\Models\Card;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;
use App\Notifications\TaskNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOverdueTaskNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event = null): void
    {
        $users = $event ? [$event->user] : User::all(); // If event is null, get all users

        foreach ($users as $user) {
            $cards = Card::where('user_project_id', $user->id) // Tukar ke 'assigned_user_id'
                ->where('due_date', '<', now())
                ->where(function ($query) {
                    $query->where('status_id', 2)
                          ->orWhere('status_id', 1);
                })
                ->get();

            foreach ($cards as $card) {
                // Check if notification already exists for this task
                $existingNotification = DB::table('notifications')
                    ->where('user_id', $user->id) // The user who receives the notification
                    ->whereJsonContains('data->type', 'overdue_task') // Ensure it's for users
                    ->whereJsonContains('data->task_id', $card->id) // Check if task_id exists in JSON data
                    ->exists();

                if (!$existingNotification) {
                    $user->notify(new TaskNotification($user, $card, 'overdue_task'));

                    // Send email notification
                    Mail::raw('Your task is overdue: ' . $card->title, function ($message) use ($user) {
                        $message->to($user->email)
                                ->subject('Task Overdue Alert');
                    });
                }
            }
        }
    }
}
