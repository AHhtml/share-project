<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // تسجيل اختصار الـ Role Middleware
        $middleware->alias([
            'role' => CheckRole::class,
        ]);

        // تحديد مسارات التوجيه عند الحماية
        $middleware->redirectTo(
            guests: '/login', // توجيه غير المسجلين عند محاولة دخول صفحات محمية
            users: '/student/dashboard' // توجيه المسجلين عند محاولة دخول صفحة login أو register
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();