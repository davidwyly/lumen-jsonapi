<?php

namespace LumenToolkit\Exceptions;

use LumenToolkit\Helpers\HttpStatus;
use LumenToolkit\Http\Controllers\ExceptionController;
use Exception;
use Throwable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable $e
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
     * @param  Request    $request
     * @param  \Exception $e
     *
     * @return Response
     */
    public function render($request, Throwable $e)
    {
        $render = null;
        if ($e instanceof ModelNotFoundException) {
            $exception_controller = new ExceptionController();
            return $exception_controller->render($request,
                                                 new \Exception($e->getMessage() . ' Model not found'));
        }

        if ($e instanceof NotFoundHttpException
            || $e->getCode() == HttpStatus::NOT_FOUND
        ) {
            $exception_controller = new ExceptionController();
            return $exception_controller->render($request,
                                                 new \Exception('Not Found', HttpStatus::NOT_FOUND));
        }

        if ($e instanceof ThrottleRequestsException) {
            $exception_controller = new ExceptionController();
            return $exception_controller->render($request,
                                                 new \Exception('Too Many Requests', HttpStatus::TOO_MANY_REQUESTS));
        }

        if ($e instanceof DecryptException) {
            $exception_controller = new ExceptionController();
            return $exception_controller->render($request,
                                                 new \Exception("Decryption issue: " . $e->getMessage(), HttpStatus::INTERNAL_SERVER_ERROR));
        }

        if ($e instanceof AuthenticationException
            || $e instanceof UnauthorizedException
        ) {
            $exception_controller = new ExceptionController();
            return $exception_controller->render($request,
                                                 new \Exception(trim('Unauthorized ' . $e->getMessage()), HttpStatus::UNAUTHORIZED));
        }

        if ($e instanceof BadRequestHttpException) {
            $exception_controller = new ExceptionController();
            return $exception_controller->render($request,
                                                 new \Exception('Bad Request', HttpStatus::BAD_REQUEST));
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            $exception_controller = new ExceptionController();
            return $exception_controller->render($request,
                                                 new \Exception('Method Not Allowed', HttpStatus::METHOD_NOT_ALLOWED));
        }

        if ($e instanceof ReflectionException) {
            $exception_controller = new ExceptionController();
            return $exception_controller->render($request,
                                                 new \Exception('Internal Server Error: ' . $e->getMessage(), HttpStatus::INTERNAL_SERVER_ERROR));
        }

        return parent::render($request, $e);
    }
}
