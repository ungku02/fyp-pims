<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Models\Card;
use App\Models\Task;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use App\Notifications\TaskNotification;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use App\Listeners\SendOverdueTaskNotification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        Paginator::useBootstrap();

        // Ensure routes in api.php have the /api prefix
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->app->getNamespace() . 'Http\Controllers')
            ->group(base_path('routes/api.php'));

        // Event listeners
        Event::listen(Login::class, SendOverdueTaskNotification::class);

        // Schedule task for overdue notifications
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->call(function () {
                $listener = new SendOverdueTaskNotification();
                $listener->handle(null); // Call the handle method directly
            })->daily();
        });
    }
}
