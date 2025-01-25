<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class ToolsController extends Controller
{
    public function index()
    {
        // You can add logic here to fetch any data needed for the tools page
        return Inertia::render('Tools');
    }
}
