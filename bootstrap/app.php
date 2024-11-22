<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e) { 
            if ($e instanceof AuthenticationException) { 
                $response = [ 
                    'status' => false, 
                    'message' => 'Unauthorized',
                    'data' => new stdClass(),  
                ]; 
                return response()->json($response, 401); 
            }

            $response = [ 
                'status' => false, 
                'message' => $e->getMessage(), 
                'data' => new stdClass(), 
            ]; 
            return response()->json($response, 500); 
        });
    })->create();
