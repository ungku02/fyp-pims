<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Inertia\Inertia;
use App\Models\Column;
use App\Models\Project;
use Illuminate\Http\Request;

class ColumnController extends Controller
{
    public function show(Request $request)
    {
        $project = $request->input('project');
        // $background = $request->input('background');
    
        // Fetch project-specific data
        $columns = Column::with(['cards', 'status'])->where('project_id', $project)->get();
        $cards = Card::with('columns')->where('column_id', $columns)->get();

    
        return Inertia::render('Kanban/KanbanBoard', [
            'columns' => $columns,
            'cards' => $cards,
            'project' => $project,
        ]);
    }

    public function create(Request $request)
    {
        \Log::info($request->all());  // Log the incoming request data
        $validatedData = $request->validate([
            'project_id' => 'required|exists:project,id', // Ensure project_id exists in projects
            'status_id' => 'required|exists:status,id',   // Ensure status_id exists in statuses
            'name' => 'required|string|max:255',
        ]);
    
        $column = Column::create($validatedData);
    
        // Optional: Return the newly created column for frontend updates
        return redirect()->back()->with('success', 'Column created successfully');
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $column = Column::with('status')->findOrFail($id);
        $column->update($validatedData);
        dd($column);

        return redirect()->back()->with('success', 'Column updated successfully');
    }

    public function delete($id)
    {
        $column = Column::findOrFail($id);
        $column->delete();

        return redirect()->back()->with('success', 'Column deleted successfully');
    }
}
