<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Card;
use App\Models\SwapTask;
use App\Models\User;
use App\Notifications\TaskNotification;
use Carbon\Carbon;

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
            'task_id' => 'required|exists:card,id',
            'user_id' => 'required|exists:users,id', // Ensure user_id is valid
        ]);

        $taskId = $request->task_id;
        $newUserId = $request->user_id; // Directly get from request

        $swapTask = SwapTask::create([
            'status' => 'pending',
            'old_user_id' => auth()->user()->id,
            'new_user_id' => $newUserId,
            'card_id' => $taskId,
        ]);

        $newUser = User::find($newUserId);
        $newUser->notify(new TaskNotification($newUser, Card::find($taskId), 'swap_request'));

        return response()->json($swapTask);
    }

    public function respondToSwap(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:accept,reject',
        ]);

        $swapTask = SwapTask::findOrFail($id);
        $swapTask->status = $request->status;
        
        // Jika status diterima, kemaskini maklumat tugas dan pengguna baru
        if ($request->status == 'accept') {
            $swapTask->new_user_id = Auth::id();
            $task = Card::find($swapTask->card_id);
            $task->user_project_id = Auth::id();
            $task->save();
        }

        $swapTask->save();

        $oldUser = User::find($swapTask->old_user_id);
        $oldUser->notify(new TaskNotification($oldUser, Card::find($swapTask->card_id), $request->status == 'accept' ? 'swap_accepted' : 'swap_rejected'));

        return redirect()->back();
    }

    public function getSwapRequests()
    {
        
            $userId = Auth::id();
            $receivedRequests = SwapTask::where('new_user_id', $userId)->get();
            $sentRequests = SwapTask::where('old_user_id', $userId)->get();

            // dd($receivedRequests, $sentRequests);

            return response()->json([
                'receivedRequests' => $receivedRequests,
                'sentRequests' => $sentRequests,
            ]);
        
    }

    public function checkOverdueTasks()
    {
        $tasks = Card::where('due_date', '<', Carbon::now())->get();

        foreach ($tasks as $task) {
            $user = $task->userRole->user;
            Mail::raw('The task is overdue: ' . $task->title, function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Task Overdue');
            });
            $user->notify(new TaskNotification($task, 'overdue'));
        }

        return response()->json(['message' => 'Overdue task notifications sent.']);
    }

}