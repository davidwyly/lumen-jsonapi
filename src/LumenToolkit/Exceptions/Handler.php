<?php

namespace LumenToolkit\Exceptions;

use LumenToolkit\Http\Status;
use LumenToolkit\Http\Controllers\ExceptionController;
use Exception;
use Throwable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use ReflectionException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  Throwable $e
     *
     * @return void
     * @throws Exception
     */
    public function report(Throwable $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request   $request
     * @param Throwable $e
     *
     * @return Response
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        $render = null;
        if ($e instanceof ModelNotFoundException) {
            $exception_controller = new ExceptionController();
            $e = new \Exception($e->getMessage() . ' Model not found', Status::INTERNAL_SERVER_ERROR);
            return $exception_controller->render($request, $e);

        }

        if ($e instanceof NotFoundHttpException
            || $e->getCode() == Status::NOT_FOUND
        ) {
            $exception_controller = new ExceptionController();
            $e = new \Exception('Not Found', Status::NOT_FOUND);
            return $exception_controller->render($request, $e);
        }

        if ($e instanceof ValidationException) {
            $exception_controller = new ExceptionController();
            $e = new \Exception('Validation issue: ' . $e->getMessage(), Status::BAD_REQUEST);
            return $exception_controller->render($request, $e);
        }

        if ($e instanceof ThrottleRequestsException) {
            $exception_controller = new ExceptionController();
            $e = new \Exception('Too Many Requests', Status::TOO_MANY_REQUESTS);
            return $exception_controller->render($request, $e);
        }

        if ($e instanceof DecryptException) {
            $exception_controller = new ExceptionController();
            $e = new \Exception("Decryption issue: " . $e->getMessage(), Status::INTERNAL_SERVER_ERROR);
            return $exception_controller->render($request, $e);
        }

        if ($e instanceof AuthenticationException
            || $e instanceof UnauthorizedException
        ) {
            $exception_controller = new ExceptionController();
            $e = new \Exception(trim('Unauthorized ' . $e->getMessage()), Status::UNAUTHORIZED);
            return $exception_controller->render($request, $e);
        }

        if ($e instanceof BadRequestHttpException) {
            $exception_controller = new ExceptionController();
            $e = new \Exception('Bad Request',Status::BAD_REQUEST);
            return $exception_controller->render($request, $e);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            $exception_controller = new ExceptionController();
            $e = new \Exception('Method Not Allowed', Status::METHOD_NOT_ALLOWED);
            return $exception_controller->render($request, $e);
        }

        if ($e instanceof ReflectionException) {
            $exception_controller = new ExceptionController();
            $e = new \Exception('Internal Server Error: ' . $e->getMessage(), Status::INTERNAL_SERVER_ERROR);
            return $exception_controller->render($request, $e);
        }

        return parent::render($request, $e);
    }
}
