<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middlewareAliases = [
        // ... existing middleware
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'active.user' => \App\Http\Middleware\CheckUserActive::class,
    ];
}