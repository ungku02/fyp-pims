<?php

use App\Models\User;
use Inertia\Inertia;
use App\Models\Project;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Models\Workspace;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $workspaces = Workspace::with('project', 'members')
    ->select('id', 'title', 'description')
    ->whereHas('members', function ($query) {
        $query->where('user_id', auth()->id());
    })
    ->get();

    return Inertia::render('Dashboard', ['workspaces' => $workspaces]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/board', [WorkspaceController::class, 'index'])->name('board');

Route::get('/project', [ProjectController::class, 'index'])->name('project');
Route::get('/project/{id}', [ProjectController::class, 'show'])->name('project/show');
Route::get('/show/project/{id}', [ProjectController::class, 'showProject'])->name('project/show-project');
Route::get('/kanban', [ProjectController::class, 'showKanban'])->name('project.kanban');
Route::get('/kanban/team', [ProjectController::class, 'showTeam'])->name('project/team');
Route::post('/submit/project', [ProjectController::class, 'create'])->name('project.create');

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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/submit', [WorkspaceController::class, 'create'])->name('workspace.create');
    Route::put('/workspaces/{id}', [WorkspaceController::class, 'update'])->name('workspace.update');
    Route::delete('delete/workspaces/{id}', [WorkspaceController::class, 'destroy'])->name('workspace.destroy');

    Route::get('/user/cards', [CardController::class, 'getUserCards']);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::get('/show/workspace/{id}', [WorkspaceController::class, 'show'])->name('workspace.show');
    
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});

require __DIR__.'/auth.php';
