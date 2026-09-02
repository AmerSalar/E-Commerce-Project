<?php

use App\Helpers\HelperFunctions;
use App\Http\Middleware\Auth\AuthenticateFromCookie;
use App\Http\Resources\Auth\AuthResource;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\Authenticate;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.cookie' => AuthenticateFromCookie::class,
        ]);
        $middleware->priority([
            AuthenticateFromCookie::class,
            Authenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson())
                return response()->json(new AuthResource((object) [
                    'message' => "You are not authenticated!"
                ]), 401);
        });

        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->is("api/*")) {
                $previous = $e->getPrevious();
                $modelName = $previous instanceof ModelNotFoundException
                    ? class_basename($previous->getModel())
                    : 'Resource or route';

                return HelperFunctions::modelNotFound($modelName);
            }
        });
    })->create();
