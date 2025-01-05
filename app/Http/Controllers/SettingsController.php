<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use App\Notifications\UserUnavailabilityNotification;

class SettingsController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings');
    }

    public function update(Request $request)
    {
        $request->validate([
            'availability' => 'required|string|in:available,unavailable',
        ]);

        $user = Auth::user();
        $user->availability = $request->availability;
        $user->save();

        // Notify project managers about the user's unavailability
        if ($request->availability === 'unavailable') {
            $projectManagers = User::whereHas('roles', function ($query) {
                $query->where('name', 'Project Manager');
            })->get();

            foreach ($projectManagers as $manager) {
                // Create a notification for the project manager
                Notification::create([
                    'user_id' => $manager->id,
                    'type' => 'availability',
                    'data' => json_encode(['message' => 'User ' . $user->name . ' is unavailable']),
                ]);

                // Send email notification to the project manager
                Mail::raw('User ' . $user->name . ' is unavailable', function ($message) use ($manager) {
                    $message->to($manager->email)
                            ->subject('User Unavailability Notification');
                });

                // Notify the project manager using the notification class
                $manager->notify(new UserUnavailabilityNotification($user));
            }
        }

        return redirect()->route('settings')->with('success', 'Status updated successfully');
    }
}
