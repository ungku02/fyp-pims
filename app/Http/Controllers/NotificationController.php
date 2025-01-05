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
        // ...
    }

    public function sendSwapResponseNotification($oldUserId, $swapTask)
    {
        // Logic to send notification to the old user
        // ...
    }
}
