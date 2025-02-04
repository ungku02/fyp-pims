<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->get();
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['read' => true]);
        return redirect()->back();
    }

    public function sendSwapRequestNotification($newUserId, $swapTask)
    {
        // Logic to send notification to the new user
        $newUser = User::find($newUserId);
        $task = Card::find($swapTask->card_id);
        Mail::raw('You have been assigned a new task: ' . $task->title, function ($message) use ($newUser) {
            $message->to($newUser->email)
                    ->subject('New Task Assigned');
        });
        $newUser->notify(new TaskNotification($task, 'assigned'));
    }

    public function sendSwapResponseNotification($oldUserId, $swapTask)
    {
        // Logic to send notification to the old user
        $oldUser = User::find($oldUserId);
        $task = Card::find($swapTask->card_id);
        if ($swapTask->status == 'accept') {
            Mail::raw('The task is overdue: ' . $task->title, function ($message) use ($oldUser) {
                $message->to($oldUser->email)
                        ->subject('Task Overdue');
            });
            $oldUser->notify(new TaskNotification($task, 'overdue'));
        }
    }
}
