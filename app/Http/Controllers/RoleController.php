<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\UserRole;
use Inertia\Inertia;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class RoleController extends Controller
{
    public function index()
    {
        $workspaces = Workspace::with('project', 'members', 'users')
        ->select('id', 'title', 'description', 'user_id')
        ->whereHas('members', function ($query) {
        $query->where('user_id', auth()->id());
        })
        ->orWhere('user_id', auth()->id())
        ->get();

        if (auth()->user()->roles()->first() != null) {
        $role = auth()->user()->roles()->first();
        } else {
        $role = null;
        }

        $notifications = auth()->user()->notifications;
        $roles = Role::all();
        return Inertia::render('RoleManagement', ['roles' => $roles, 'workspaces' => $workspaces, 'notifications' => $notifications, 'role' => $role]);

    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Role::create($validatedData);

        return redirect()->back();
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        if ($role->name === 'Project Manager') {
            return redirect()->back()->withErrors(['error' => 'Editing the Project Manager role is not allowed.']);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $role->update($validatedData);

        return redirect()->back();
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->name === 'Project Manager') {
            return redirect()->back()->withErrors(['error' => 'Deleting the Project Manager role is not allowed.']);
        }

        $usersWithRole = UserRole::where('role_id', $role->id)->get();

        if ($usersWithRole->isNotEmpty()) {
            return redirect()->back()->withErrors(['error' => 'Role is assigned to users. Please reassign their roles before deleting.']);
        }

        $role->delete();

        return redirect()->back();
    }

    public function getUsers(Role $role)
    {
        $users = UserRole::where('role_id', $role->id)->with('user', 'project')->get();
        return Inertia::render('RoleUsers', ['users' => $users]);
    }

    public function reassignRole(Request $request, Role $role): RedirectResponse
    {
        $validatedData = $request->validate([
            'new_role_id' => 'required|exists:roles,id',
        ]);

        UserRole::where('role_id', $role->id)->update(['role_id' => $validatedData['new_role_id']]);

        $role->delete();

        return redirect()->back();
    }
}
