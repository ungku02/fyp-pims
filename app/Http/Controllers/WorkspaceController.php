<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Workspace;
use Illuminate\Http\Request;
use App\Models\WorkspaceMembers;
use Illuminate\Validation\Rules;
use Illuminate\Http\RedirectResponse;
use App\Models\UserRole;
use App\Models\Notification;

class WorkspaceController extends Controller
{
    public function index()
    {
        $workspaces = Workspace::select('id', 'title', 'description')->where('user_id', auth()->id())->get();
        return Inertia::render('Board', ['workspaces' => $workspaces]);
    }

    public function create(Request $request) : RedirectResponse 
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'members' => 'array',
            'members.*.email' => 'required|email|exists:users,email',
        ]);

        $validatedData['user_id'] = auth()->id();
        
        // Create a new workspace with the validated data
        $workspace = Workspace::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'user_id' => $validatedData['user_id']
        ]);

        // Add members to the workspace
        foreach ($validatedData['members'] as $member) {
            $user = User::where('email', $member['email'])->first();
            WorkspaceMembers::create([
                'user_id' => $user->id,
                'workspace_id' => $workspace->id,
            ]);

            // Create a notification for the user
            Notification::create([
                'user_id' => $user->id,
                'type' => 'workspace',
                'data' => json_encode(['message' => 'You have been added to a new workspace: ' . $workspace->title]),
            ]);
        }
    
        // Redirect to the 'board' route
        return redirect()->back();
    }

    public function show($id)
    {
        $workspace = Workspace::with(['members.users', 'members', 'project'])->find($id);
        $workspaces = Workspace::with('project', 'members')
        ->select('id', 'title', 'description')
        ->whereHas('members', function ($query) {
            $query->where('user_id', auth()->id());
        })
        ->get();
    
        $members = $workspace->members()->paginate(5); // Paginate members
        $notifications = auth()->user()->notifications; // Get user notifications

        // dd($workspaces);
        return Inertia::render('Workspace/Show', [
            'workspace' => $workspace,
            'workspaces' => $workspaces,
            'members' => $members, // Pass paginated members
            'notifications' => $notifications, // Pass notifications
        ]);
    }

    public function addMember(Request $request, $id): RedirectResponse
    {
        $validatedData = $request->validate([
            'email' => 'required|email|exists:users,email',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::where('email', $validatedData['email'])->first();
        $workspace = Workspace::find($id);

        if ($workspace && $user) {
            UserRole::create([
                'user_id' => $user->id,
                'role_id' => $validatedData['role_id'],
                'project_id' => $workspace->project->id,
            ]);

            WorkspaceMembers::create([
                'user_id' => $user->id,
                'workspace_id' => $workspace->id,
            ]);
        }

        return redirect()->back();
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        $workspace = Workspace::find($id);
        if ($workspace) {
            $workspace->update($validatedData);
        }

        return redirect()->back();
    }

    public function destroy($id)
    {
        $workspace = Workspace::find($id);
        // dd($workspace);
        if ($workspace) {
            $workspace->delete();
        }

        return redirect()->route('dashboard');
    }
    
}
