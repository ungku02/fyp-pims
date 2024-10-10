<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;



class WorkspaceController extends Controller
{
    public function index()
    {
        $workspaces = Workspace::select('id', 'title', 'description')->get();
        return Inertia::render('Board', ['workspaces' => $workspaces]);
    }

    public function create(Request $request) : RedirectResponse 
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);
        
        // Create a new workspace with the validated data
        $workspace = Workspace::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
        ]);
    
        // Redirect to the 'board' route
        return redirect()->back();
    }
    
}
