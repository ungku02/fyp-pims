<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // ...existing code...

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            // ...existing code...
        ],

        'api' => [
            // ...existing code...
            \App\Http\Middleware\Cors::class,
        ],
    ];

    // ...existing code...
}
