<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function (Request $request, \Throwable $e): bool {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->is('livewire/*')) {
                return redirect()->guest(route('login', ['reason' => 'session-expired']));
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('livewire/*')) {
                return redirect()->guest(route('login', ['reason' => 'session-expired']));
            }
        });

        $exceptions->respond(function ($response, \Throwable $e, Request $request) {
            if ($request->is('livewire/*') && in_array($response->getStatusCode(), [401, 419], true)) {
                return redirect()->guest(route('login', ['reason' => 'session-expired']));
            }

            return $response;
        });
    })->create();
