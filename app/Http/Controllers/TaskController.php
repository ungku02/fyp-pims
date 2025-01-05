<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Card;
use App\Models\SwapTask;
use App\Models\User;

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

    public function requestSwap(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:cards,id',
            'new_user_id' => 'required|exists:users,id',
        ]);

        $swapTask = SwapTask::create([
            'status' => 'pending',
            'old_user_id' => Auth::id(),
            'new_user_id' => $request->new_user_id,
            'card_id' => $request->task_id,
        ]);

        // Send notification to the new user
        // ...

        return response()->json($swapTask);
    }

    public function respondToSwap(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:accept,reject',
        ]);

        $swapTask = SwapTask::findOrFail($id);
        $swapTask->status = $request->status;
        if ($request->status == 'accept') {
            $swapTask->new_user_id = Auth::id();
        }
        $swapTask->save();

        // Send notification to the old user
        // ...

        return response()->json($swapTask);
    }

    public function getSwapRequests()
    {
        $userId = Auth::id();
        $receivedRequests = SwapTask::where('new_user_id', $userId)->get();
        $sentRequests = SwapTask::where('old_user_id', $userId)->get();

        return response()->json([
            'receivedRequests' => $receivedRequests,
            'sentRequests' => $sentRequests,
        ]);
    }
}