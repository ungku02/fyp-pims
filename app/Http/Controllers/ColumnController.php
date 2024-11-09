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
        $columns = Column::with('cards')->where('project_id', $project)->get();
        $cards = Card::with('columns')->where('column_id', $columns)->get();

    
        return Inertia::render('Kanban/KanbanBoard', [
            'columns' => $columns,
            'cards' => $cards,
            'project' => $project,
        ]);
    }
    


}
