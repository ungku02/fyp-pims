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
use Illuminate\Support\Facades\Mail;

class WorkspaceController extends Controller
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
        return Inertia::render('Board', ['workspaces' => $workspaces, 'notifications' => $notifications, 'role' => $role]);
    }

    public function dashboard()
    {
         $workspaces = Workspace::with('project', 'members', 'users')
         ->select('id', 'title', 'description', 'user_id')
         ->whereHas('members', function ($query) {
         $query->where('user_id', auth()->id());
         })
         ->get();
         // dd($workspaces);

         return Inertia::render('Dashboard', ['workspaces' => $workspaces]);
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

            // Send email notification to the user
            Mail::raw('You have been added to a new workspace: ' . $workspace->title, function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('New Workspace Added');
            });
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

    public function addMember(Request $request, $id)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $validatedData['email'])->first();
        $workspace = Workspace::find($id);

        if ($workspace && $user) {
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

    public function deleteMember(Request $request, $workspaceId, $memberId): RedirectResponse
    {
        $user = auth()->user();
        $role = $user->roles()->first();

        if ($role && $role->name === 'Project Manager') {
            // Ensure you're matching on the composite keys: workspace_id and user_id
            $deleted = WorkspaceMembers::where('workspace_id', $workspaceId)
            ->where('user_id', $memberId)
            ->delete();

            if ($deleted) {
                return redirect()->back()->with('success', 'Member removed successfully.');
            } else {
                return redirect()->back()->with('error', 'Failed to remove member. The record may not exist.');
            }

        } else {
            return redirect()->back()->with('error', 'Only project managers can delete workspace members.');
        }
    }

    
}
