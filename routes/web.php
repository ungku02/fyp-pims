<?php

use Inertia\Inertia;
use App\Models\Project;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\WorkspaceController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/board', [WorkspaceController::class, 'index'])->name('board');


Route::get('/project', [ProjectController::class, 'index'])->name('project');
Route::get('/project/{id}', [ProjectController::class, 'show'])->name('project/show');
Route::get('/kanban', [ProjectController::class, 'showKanban'])->name('project/kanban');
Route::post('/submit/project', [ProjectController::class, 'create'])->name('project.create');

Route::get('/card', [CardController::class, 'index'])->name('card.index');
Route::post('/submit/card', [CardController::class, 'create'])->name('card.create');






Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/submit', [WorkspaceController::class, 'create'])->name('workspace.create');

});

require __DIR__.'/auth.php';
