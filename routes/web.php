<?php

use App\Models\Card;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\FcmController;
use App\Notifications\TaskNotification;
use App\Http\Controllers\CardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ToolsController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TimelineController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\RegisteredUserController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $workspaces = Workspace::with('project', 'members', 'users')
    ->select('id', 'title', 'description', 'user_id')
    ->whereHas('members', function ($query) {
        $query->where('user_id', auth()->id());
    })
    ->orWhere('user_id', auth()->id())
    ->get();

    if (auth()->user()->roles()->first() != null) {
        $role = auth()->user()->roles()->first();
    } else {
        $role = null;
    }

      $events = auth()->user()->events;
      $notifications = auth()->user()->notifications;
    //   dd($notifications);
    // dd($workspaces);

    return Inertia::render('Dashboard', ['workspaces' => $workspaces, 'notifications' => $notifications, 'role' => $role, 'events' => $events]);
})->middleware(['auth', 'verified'])->name('dashboard');



Route::middleware('auth')->group(function () {
    Route::get('/board', [WorkspaceController::class, 'index'])->name('board');

Route::get('/project', [ProjectController::class, 'index'])->name('project');
Route::get('/project/{id}', [ProjectController::class, 'show'])->name('project/show');
Route::get('/show/project/{id}', [ProjectController::class, 'showProject'])->name('project/show-project');
Route::get('/kanban', [ProjectController::class, 'showKanban'])->name('project.kanban');
Route::get('/kanban/team', [ProjectController::class, 'showTeam'])->name('project/team');
Route::post('/submit/project', [ProjectController::class, 'create'])->name('project.create');
Route::put('/project/{id}', [ProjectController::class, 'update'])->name('project.update');
Route::delete('/project/{id}', [ProjectController::class, 'destroy'])->name('project.destroy');
Route::get('/project/{id}/members', [ProjectController::class, 'getProjectMembers'])->name('project.members');
Route::post('/project/{id}/add-member', [ProjectController::class, 'addMember'])->name('project.addMember');
Route::put('/project/{id}/update-member-role/{memberId}', [ProjectController::class, 'updateMemberRole'])->name('project.updateMemberRole');

Route::get('/calendar', [ProjectController::class, 'showCalendar'])->name('calendar');

Route::get('/card', [CardController::class, 'index'])->name('card.index');
Route::post('/submit/card', [CardController::class, 'create'])->name('card.create');
Route::post('/submit/column', [ColumnController::class, 'create'])->name('column.create');
Route::put('/card/{id}', [CardController::class, 'update'])->name('card.update');
Route::delete('/card/{id}', [CardController::class, 'delete'])->name('card.delete');
Route::put('/column/{id}', [ColumnController::class, 'update'])->name('column.update');
Route::delete('/column/{id}', [ColumnController::class, 'delete'])->name('column.delete');

Route::post('/search-user', [RegisteredUserController::class, 'searchByEmail']);

Route::get('/swap-tasks', [ProjectController::class, 'showSwapTasks'])->name('project.swapTasks');

Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
Route::post('/settings/update', [SettingsController::class, 'update'])->name('settings.update');

Route::get('/tools', [ToolsController::class, 'index'])->name('tools.index');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/submit', [WorkspaceController::class, 'create'])->name('workspace.create');
    Route::put('/workspaces/{id}', [WorkspaceController::class, 'update'])->name('workspace.update');
    Route::delete('delete/workspaces/{id}', [WorkspaceController::class, 'destroy'])->name('workspace.destroy');
    Route::post('/workspaces/{id}/members', [WorkspaceController::class, 'addMember'])->name('workspace.addMember');
    Route::delete('/workspaces/{workspaceId}/members/{memberId}', [WorkspaceController::class, 'deleteMember'])->name('workspace.deleteMember');

    Route::get('/user/cards', [CardController::class, 'getUserCards']);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::get('/show/workspace/{id}', [WorkspaceController::class, 'show'])->name('workspace.show');
    
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    // Route::middleware('can:manageRoles,App\Models\Workspace')->group(function () {
        Route::post('/roles/store', [RoleController::class, 'store'])->name('roles.store');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::get('/roles/{role}/users', [RoleController::class, 'getUsers'])->name('roles.users');
        Route::post('/roles/{role}/reassign', [RoleController::class, 'reassignRole'])->name('roles.reassign');
    // });

    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::put('/events/{id}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{id}', [EventController::class, 'destroy'])->name('events.destroy');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::put('/users/{user}/availability', [UserController::class, 'updateAvailability'])->name('users.updateAvailability');
    Route::get('/users/filter', [UserController::class, 'filter'])->name('users.filter');

    // Swap Task Routes
    Route::get('/swap-tasks/get', [TaskController::class, 'getUserTasks'])->name('swapTasks.getUserTasks');
    Route::post('/swap-tasks/request', [TaskController::class, 'requestSwap'])->name('swapTasks.request');
    Route::post('/swap-tasks/respond/{id}', [TaskController::class, 'respondToSwap'])->name('swapTasks.respond');
    Route::get('/swap-tasks/requests', [TaskController::class, 'getSwapRequests'])->name('swapTasks.requests');

    Route::get('/timeline', [TimelineController::class, 'show'])->name('timeline');

    Route::post('/save-fcm-token', [FcmController::class, 'saveFcmToken']);
    Route::get('/send-notification', [FcmController::class, 'sendNotification']);


});

require __DIR__.'/auth.php';
