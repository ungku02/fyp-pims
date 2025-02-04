<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Workspace;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Notifications\UserUnavailabilityNotification;

class SettingsController extends Controller
{
    public function index()
    {
        $workspace = Workspace::with('project', 'members', 'users')
            ->select('id', 'title', 'description', 'user_id')
            ->whereHas('members', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->orWhere('user_id', auth()->id())
            ->get();
        $notifications = auth()->user()->notifications;
        return Inertia::render('Settings', ['notifications' => $notifications, 'workspaces' => $workspace]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'availability' => 'required|string|in:available,unavailable',
            'start_date' => 'required_if:availability,unavailable|date|nullable',
            'end_date' => 'required_if:availability,unavailable|date|after_or_equal:start_date|nullable',
        ]);

        $user = Auth::user();
        $user->availability = $request->availability;
        $user->start_date = $request->availability === 'available' ? null : $request->start_date;
        $user->end_date = $request->availability === 'available' ? null : $request->end_date;
        $user->save();

        // Notify project managers about the user's unavailability
        if ($request->availability === 'unavailable' || $request->availability === 'available') {
            $projectManagers = User::whereHas('roles', function ($query) {
                $query->where('name', 'Project Manager');
            })->get();

            foreach ($projectManagers as $manager) {
                // Send email notification to the project manager
                Mail::raw('User ' . $user->name . ' is ' . $request->availability . ' from ' . $request->start_date . ' to ' . $request->end_date, function ($message) use ($manager) {
                    $message->to($manager->email)
                            ->subject('User Availability Notification');
                });

                // Notify the project manager using the notification class
                $manager->notify(new UserUnavailabilityNotification($user));
            }
        }

        return redirect()->route('settings')->with('success', 'Status updated successfully');
    }
}
