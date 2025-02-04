<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Column;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Notifications\TaskNotification;
use Illuminate\Support\Facades\Storage;

class CardController extends Controller
{
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'column_id' => 'required',
            'status_id' => 'required',
            'due_date' => 'nullable|date',
            'urgency' => 'nullable',
            'attachment.*' => 'nullable|file', // Validate each file in the attachments array
            'user_project_id' => 'nullable',
        ]);

        $attachments = [];
        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                $path = $file->store('attachments', 'public');
                $attachments[] = [
                    'url' => Storage::url($path), // Generate public URL
                    'name' => $file->getClientOriginalName(), // Store original file name
                ];
            }
        }

        $validatedData['attachment'] = json_encode($attachments); // Store attachments as JSON

        $card = Card::create($validatedData);

        $user = User::where('id', $validatedData['user_project_id'])->first();

        // Mail::raw('You have been assigned a new task: ' . $validatedData['title'], function ($message) use ($user) {
        //     $message->to($user->email)
        //             ->subject('New Task Assigned');
        // });

        $user->notify(new TaskNotification($user, $card, 'new_task'));

        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'column_id' => 'required',
            'status_id' => 'required',
            'due_date' => 'required|date',
        ]);

        $card = Card::findOrFail($id);
        $card->update($validatedData);

        // Check if the task is completed
        if ($validatedData['status_id'] == 3) {
            $user = User::where('id', $card->user_project_id)->first();
            if ($user && $user->fcm_token) {
                $notificationData = [
                    'user_id' => $user->id,
                    'title' => 'Task Completed',
                    'body' => 'Your task "' . $card->title . '" has been marked as completed.',
                ];

                Http::post(route('taskCompletedNotification'), $notificationData);
            }
        }

        // Check if the task is overdue
        if (now()->greaterThan($card->due_date)) {
            $user = User::where('id', $card->user_project_id)->first();
            Mail::raw('Your task is overdue: ' . $card->title, function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Task Overdue');
            });
        }

        return response()->json(['message' => 'Card updated successfully']);
    }

    public function delete($id)
    {
        $card = Card::findOrFail($id);
        $card->delete();

        return response()->json(['message' => 'Card deleted successfully']);
    }

    public function getUserCards()
    {
        $userId = Auth::id();
        $cards = Card::with(['userRole.users', 'userRole.roles', 'columns.status'])
            ->whereHas('userRole', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->get();

        return response()->json($cards);
    }
}
