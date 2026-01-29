<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'active' => \App\Http\Middleware\CheckActiveStatus::class,
            'profile.complete' => \App\Http\Middleware\EnsureProfileComplete::class,
        ]);

        // Apply locale to all web requests
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        // Exclude Webhook from CSRF
        $middleware->validateCsrfTokens(except: [
            '/webhook/whatsapp',
        ]);

        // Trust Cloudflare proxies for proper HTTPS detection
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
