<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Column;
use App\Models\Project;
use App\Models\UserRole;
use App\Models\Workspace;
use App\Helper\BankHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Card;

class ProjectController extends Controller
{
    public function index()
    {
        
        return Inertia::render('project/Project');
    }

    public function showTeam()
    {
        // Retrieve the project ID from the request
        $project = request()->input('project');
        
        // Retrieve the background image from the request
        $background = request()->input('background');

        // Retrieve the members associated with the project
        $members = UserRole::with(['users', 'roles'])->where('project_id', $project)->get();

        // dd($members);
        return Inertia::render('Kanban/TeamMembers', [
            'project' => $project,   // Pass the project ID
            'background' => $background, // Pass the background image
            'members' => $members, // Pass the members
        ]);
    }

    public function showProject($id)
    {
        $workspace = Workspace::with('project')->findOrFail($id);
        

        // Check if the workspace has projects
        $projects = $workspace->project;

        // Fetch roles from BankHelper
        $roles = BankHelper::getRoles();

        return Inertia::render('project/ShowProject', [
            'projects' => $projects,
            'hasProjects' => $projects->isNotEmpty(), // Pass a boolean to check if projects exist
            'workspace' => $workspace,
            'roles' => $roles
        ]);
    }

    public function show($id)
    {
        $project = Project::with('userRole.users', 'userRole.roles', 'workspace', 'column')->find($id);
        if (!$project) {
            abort(404, 'Project not found');
        }

        // Paginate members data
        $members = UserRole::with(['users', 'roles'])
                           ->where('project_id', $id)
                           ->paginate(5);

        $notifications = auth()->user()->notifications; // Get user notifications


        return Inertia::render('project/Show', [
            'project' => $project, // Pass the project title
            'members' => $members, // Pass paginated members
            'notifications' => $notifications, // Pass notifications
        ]);
    }

    public function showKanban()
    {
        // Retrieve the project ID from the request
        $projectId = request()->input('project');
        
        // Retrieve the background image from the request
        $background = request()->input('background');

        // Retrieve columns associated with the specified project
        $columns = Column::with(['cards.userRole.users', 'cards.userRole.roles', 'status'])
                         ->where('project_id', $projectId)
                         ->get();
        $project = $project = Project::with('userRole.users', 'userRole.roles', 'workspace', 'column')->find($projectId);

        // Fetch status from BankHelper
        $status = BankHelper::getStatus();

        // Retrieve the members associated with the project
        $members = UserRole::with(['users', 'roles'])
                           ->where('project_id', $projectId)
                           ->get();


        // Return the Inertia view with the necessary data
        return Inertia::render('Kanban/KanbanBoard', [
            'columns' => $columns,    // Pass columns to the component
            'projectId' => $projectId,   // Pass the project ID
            'background' => $background, // Pass the background image
            'status' => $status, // Pass the status value for dropdown
            'members' => $members, // Pass the members
            'project' => $project, // Pass the project title

        ]);
    }

    public function showCalendar()
    {
        // Retrieve the project ID from the request
        $project = request()->input('project');
        
        // Retrieve the background image from the request
        $background = request()->input('background');

        // Retrieve cards with due dates for the logged-in user
        $userId = Auth::id();
        $cards = Card::with(['userRole.users', 'userRole.roles'])
            ->whereHas('userRole', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->whereNotNull('due_date')->get();
            // dd($cards);

        // Return the Inertia view with the necessary data
        return Inertia::render('Kanban/Calendar', [
            'cards' => $cards,    // Pass cards to the component
            'project' => $project,   // Pass the project ID
            'background' => $background, // Pass the background image
        ]);
    }

    public function create(Request $request)
    {
        // Validate the input fields
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'image' => 'required|string|max:255',
            'workspace_id' => 'required', // Ensure workspace exists
            'members' => 'array', // Expect an array of members, each with 'email' and 'role_id'
            'members.*.email' => 'required|email|exists:users,email', // Validate each email is valid and exists
            'members.*.role_id' => 'required|exists:role,id', // Each member must have a valid role_id
        ]);

        // Create the project and store it in the `projects` table
        $project = Project::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'image' => $validatedData['image'],
            'workspace_id' => $validatedData['workspace_id'],
        ]);

        // If members are provided, store the user roles in the `user_role` table
        if (isset($validatedData['members']) && !empty($validatedData['members'])) {
            foreach ($validatedData['members'] as $member) {
                // Get the user by email
                $user = User::where('email', $member['email'])->firstOrFail();

                // Store user role in the `user_role` table
                UserRole::create([
                    'user_id' => $user->id,      // Get the user's ID from the email
                    'role_id' => $member['role_id'],  // ID of the role
                    'project_id' => $project->id,     // ID of the created project
                ]);
            }
        }

        // Redirect back to the previous page
        return redirect()->back();
    }

    public function getUserProjects()
    {
        $userId = Auth::id();
        $projects = Project::whereHas('userRole', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with('workspace')->get();

        return response()->json($projects);
    }

    public function showSwapTasks()
    {
        // Retrieve the project ID from the request
        $projectId = request()->input('project');

        // Retrieve the background image from the request
        $background = request()->input('background');

        // Retrieve tasks assigned to the logged-in user
        $project = Project::with('userRole.users', 'userRole.roles', 'workspace', 'column')->find($projectId);
        // dd($project);
        $userId = Auth::id();
        $tasks = Card::with(['userRole.users', 'userRole.roles'])
            ->whereHas('userRole', function ($query) use ($userId, $projectId) {
                $query->where('user_id', $userId)
                      ->where('project_id', $projectId);
            })->get();

      

        // Retrieve all users in the same project
        $members = UserRole::with(['users', 'roles'])
            ->where('project_id', $projectId)
            ->get();

            // dd($tasks);

        // Return the Inertia view with the necessary data
        return Inertia::render('Kanban/SwapTasks', [
            'tasks' => $tasks,    // Pass tasks to the component
            'projectId' => $projectId,   // Pass the project ID
            'background' => $background, // Pass the background image
            'members' => $members, // Pass the members
            'project' =>$project
        ]);
    }
}
