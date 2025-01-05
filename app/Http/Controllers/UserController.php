<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        $notifications = auth()->user()->notifications;
        $workspaces = Workspace::with('project', 'members', 'users')->where('user_id', auth()->id())->get();

        if (auth()->user()->roles()->first() != null) {
            $role = auth()->user()->roles()->first();
        } else {
            $role = null;
        }
        return Inertia::render('UserManagement', [
            'users' => $users, 
            'notifications' => $notifications, 
            'workspaces' => $workspaces,
            'role' => $role
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back(); 
    }

    public function updateAvailability(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'availability' => 'required|string|in:available,unavailable',
        ]);

        $user->availability = $validatedData['availability'];
        $user->save();

       return Inertia::location(route('users.index'));
    }

    public function filter(Request $request)
    {
        $query = User::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->input('email') . '%');
        }

        if ($request->filled('availability')) {
            $query->where('availability', $request->input('availability'));
        }

        $users = $query->paginate(10);

        $notifications = auth()->user()->notifications;
        $workspaces = Workspace::with('project', 'members', 'users')->where('user_id', auth()->id())->get();

        if (auth()->user()->roles()->first() != null) {
            $role = auth()->user()->roles()->first();
        } else {
            $role = null;
        }

        return Inertia::render('UserManagement', [
            'users' => $users,
            'notifications' => $notifications,
            'workspaces' => $workspaces,
            'role' => $role
        ]);
    }
}
