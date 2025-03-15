<?php

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\AuthtificationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\QueryException;
use Illuminate\Auth\Access\AuthorizationException;

use Illuminate\Support\Facades\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias(['admin'=>AdminMiddleware::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            return Response::json(['error' => 'Resource not found'], 404);
        });

        $exceptions->render(function (ModelNotFoundException $exception, Request $request) {
            return Response::json(['error' => 'Resource not found'], 404);
        });

        $exceptions->render(function (AuthorizationException $exception, Request $request) {
            return Response::json(['error' => 'This action is unauthorized'], 403);
        });

        $exceptions->render(function (AccessDeniedHttpException $exception, Request $request) {
            return Response::json(['error' => 'This action is unauthorized'], 403);
        });

        $exceptions->render(function (MethodNotAllowedException $exception, Request $request) {
            return Response::json(['error' => "Method not allowed or route doesn't exist"], 405);
        });

        $exceptions->render(function (MethodNotAllowedHttpException $exception, Request $request) {
            return Response::json(['error' => "Method not allowed or route doesn't exist"], 405);
        });

        // Catch all other exceptions
        $exceptions->render(function (\Throwable $exception, Request $request) {
            $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getCode() : 500;
            return Response::json(['error' => 'An error occurred', 'message' => $exception->getMessage()], $statusCode);
        });

        $exceptions->render(function (QueryException $exception, Request $request) {
            return Response::json(['error' => "An error occurred while retrieving data. Please try again later."], 500);
        });

        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            return Response::json(['error' => "You have to login first"], 401);
        });
    })->create();
