
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Card;

class TaskController extends Controller
{
    public function getUserTasks()
    {
        $userId = Auth::id();
        $tasks = Card::whereHas('userRole', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        return response()->json($tasks);
    }
}