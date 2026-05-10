<?php

// ... (existing code)

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, string>>
     */
    protected $middlewareGroups = [
        'web' => [
            // ... (existing web middleware)
        ],

        'api' => [
            // ... (existing api middleware)
        ],
    ];

    /**
     * The application's route middleware aliases.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, string>
     */
    protected $routeMiddleware = [
        // ... (existing middleware aliases)
        'admin' => \App\Http\Middleware\AdminMiddleware::class, // <-- ADD THIS
        'profile.gate' => \App\Http\Middleware\ProfileGate::class, // <-- AND THIS
    ];
}