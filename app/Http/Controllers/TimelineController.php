<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class TimelineController extends Controller
{
    public function show()
    {
        // Sample data for the timeline
        $events = [
            ['id' => 1, 'title' => 'Project Kickoff', 'description' => 'Initial project kickoff meeting.', 'date' => '2023-01-01'],
            ['id' => 2, 'title' => 'Design Phase', 'description' => 'Design phase of the project.', 'date' => '2023-02-01'],
            ['id' => 3, 'title' => 'Development Phase', 'description' => 'Development phase of the project.', 'date' => '2023-03-01'],
            ['id' => 4, 'title' => 'Testing Phase', 'description' => 'Testing phase of the project.', 'date' => '2023-04-01'],
            ['id' => 5, 'title' => 'Project Launch', 'description' => 'Official launch of the project.', 'date' => '2023-05-01'],
        ];

        return Inertia::render('Timeline/TimelinePage', ['events' => $events]);
    }
}
