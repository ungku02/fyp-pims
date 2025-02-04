<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\FcmController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user/tasks', [TaskController::class, 'getUserTasks']);
Route::middleware('auth:sanctum')->get('/user/cards', [CardController::class, 'getUserCards']);

Route::post('/save-fcm-token', [FcmController::class, 'saveFcmToken']);
Route::post('/test-notification', [FcmController::class, 'testNotification']);

Route::post('/send-notification', [FcmController::class, 'sendNotification']);
Route::post('/task-completed-notification', [FcmController::class, 'taskCompletedNotification'])->name('taskCompletedNotification');