<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Column;
use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        
        return Inertia::render('project/Project');
    }

    public function show($id)
{
    $workspace = Workspace::with('project')->findOrFail($id);

    // Check if the workspace has projects
    $projects = $workspace->project;

    return Inertia::render('project/ShowProject', [
        'projects' => $projects,
        'hasProjects' => $projects->isNotEmpty(), // Pass a boolean to check if projects exist
        'workspace' => $workspace
    ]);
}

public function showKanban()
{
    // Retrieve the project ID from the request
    $project = request()->input('project');
    
    // Retrieve the background image from the request
    $background = request()->input('background');

    // Retrieve columns associated with the specified project
    $columns = Column::with('cards')->where('project_id', $project)->get();

    // $cards = Card::

    // Return the Inertia view with the necessary data
    return Inertia::render('Kanban/KanbanBoard', [
        'columns' => $columns,    // Pass columns to the component
        'project' => $project,   // Pass the project ID
        'background' => $background, // Pass the background image
    ]);
}

public function create (Request $request)
{
    $validatedData = $request->validate([
        'title'=> 'required|string|max:255',
        'description' => 'required|string|max:255',
        'image' => 'required|string|max:255',
        'workspace_id' => 'required'
    ]);

    // $validatedData['workspace_id'] = Workspace::with('project')->findOrFail('id');

    $project = Project::create($validatedData);

    return redirect()->back();
}


}
