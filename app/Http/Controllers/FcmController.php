<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Http;

class FcmController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function saveFcmToken(Request $request)
    {
        $validated = $request->validate([
            'fcm_token' => 'required|string',
        ]);

        // Save the token to the database or wherever it's needed
        $user = auth()->user();
        $user->update(['fcm_token' => $validated['fcm_token']]);

        return response()->json(['message' => 'FCM token saved successfully.']);
    }

    public function sendNotification()
    {
        // Ambil semua pengguna yang mempunyai FCM token
        $tokens = User::whereNotNull('fcm_token')
            ->latest('updated_at') // Ambil yang paling baru ikut waktu update
            ->value('fcm_token'); // Ambil hanya satu nilai token
        // dd($tokens);

        if (empty($tokens)) {
            return response()->json(['message' => 'Tiada pengguna dengan FCM token'], 400);
        }

        // Notifikasi contoh
        $notificationData = [
            'title' => 'Ujian FCM Berjaya!',
            'body' => 'Ini adalah contoh notifikasi FCM kepada pengguna yang berdaftar.',
        ];

        // Hantar permintaan ke Firebase Cloud Messaging (FCM)
        $response = Http::withHeaders([
            'Authorization' => 'key=' . env('FCM_SERVER_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', [
            'registration_ids' => $tokens, // Gunakan 'registration_ids' untuk hantar kepada ramai pengguna
            'notification' => $notificationData,
        ]);

        // Paparkan respons daripada FCM
        return response()->json([
            'message' => 'Permintaan dihantar ke FCM',
            'fcm_response' => $response->json(),
            'tokens' => $tokens,
        ]);
    }

    public function testNotification(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'title' => 'required|string',
            'body' => 'required|string',
            'data' => 'nullable|array',
        ]);

        $token = $request->input('token');
        $title = $request->input('title');
        $body = $request->input('body');
        $data = $request->input('data', []);

        $this->firebaseService->sendNotification($token, $title, $body, $data);

        return response()->json(['message' => 'Notification sent successfully.']);
    }

    public function taskCompletedNotification(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $user = User::find($request->input('user_id'));

        if (!$user || !$user->fcm_token) {
            return response()->json(['message' => 'User not found or FCM token missing'], 400);
        }

        $this->firebaseService->sendNotification($user->fcm_token, $request->input('title'), $request->input('body'));

        return response()->json(['message' => 'Notification sent successfully.']);
    }
}
