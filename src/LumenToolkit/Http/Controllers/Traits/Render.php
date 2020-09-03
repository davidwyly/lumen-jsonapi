<?php 

declare(strict_types=1);

namespace LumenToolkit\Http\Controllers\Traits;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Collection;
use LumenToolkit\Helpers\Str;
use LumenToolkit\Http\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use LumenToolkit\Models\DataModel;
use \Illuminate\Database\QueryException;
use \Illuminate\Validation\ValidationException;
use LumenToolkit\Exceptions\ControllerException;
use ReflectionException;
use Symfony\Component\HttpFoundation\Response;

trait Render
{
    /**
     * @param Request                                         $request
     * @param DataModel|EloquentModel|Collection|array|object $data
     * @param int                                             $http_code
     * @param bool                                            $idempotent
     *
     * @return Response
     */
    protected function renderSuccess(Request $request, $data, $http_code = Status::OK, $idempotent = false): Response
    {
        if (is_subclass_of($data, DataModel::class)) {
            try {
                $reflection = new \ReflectionClass($data);
                $data = [
                    'id' => (string)$data->id,
                    'type' => $reflection->getShortName(),
                    'attributes' => $data->attributesToArray(),
                    'relationships' => $data->relationsToArray(),
                ];
            } catch (ReflectionException $e) {
                return $this->renderFail($request, $e);
            }
        }

        $response_data = [
            'data' => $data,
            'links' => [
                'self' => $request->path(),
            ],
            'jsonapi' => [
                'version' => "1.0",
            ]
        ];

        if (env('APP_ENV') == "local" && env('SHOW_API_META') === true) {
            $response_data['meta'] = [
                'method'     => $request->getMethod(),
                'success'    => true,
                'idempotent' => $idempotent,
                'request'    => $request->all(),
            ];
        }

        if (Str::contains($request->header('accept'), 'msgpack')) {
            /** @noinspection PhpUndefinedFunctionInspection */
            $response = response(msgpack_pack($response_data), $http_code, []);
        } else {
            $response = response()->json($response_data, $http_code, [], JSON_PRETTY_PRINT);
        }

        return $response;
    }

    /**
     * @param Request                                         $request
     * @param DataModel|EloquentModel|Collection|array|object $data
     * @param int                                             $http_code
     *
     * @return Response
     */
    protected function renderIdempotent(Request $request, $data, $http_code = Status::OK): Response
    {
        return $this->renderSuccess($request, $data, $http_code, true);
    }

    /**
     * @param Request $request
     * @param Exception $e
     *
     * @return Response
     */
    protected function renderFail(Request $request, Exception $e): Response
    {
        $error_code = $e->getCode();

        // an error code of '0' really should become a generic server error of '500'
        if ($error_code == 0) {
            $error_code = Status::INTERNAL_SERVER_ERROR;
        }

        // if there's a query exception, set error code to 500; otherwise this can cause issues on the response
        if (get_class($e) == QueryException::class) {
            $error_code = Status::INTERNAL_SERVER_ERROR;
        }

        $title = null;
        // if there's a validation exception, set error code to 400; also spit out what went wrong in the description
        $description = null;
        if (get_class($e) == ValidationException::class) {
            $error_code = Status::BAD_REQUEST;
            /** @var ValidationException $e */
            $response = $e->getResponse();
            /** @var JsonResponse $response */
            $description = $response->getOriginalContent();
        }

        if (get_class($e) == AuthenticationException::class) {
            $error_code = Status::UNAUTHORIZED;
            $msg = json_decode($e->getMessage());
            $title = $msg->error;
            $description = $msg->message;
        }

        if (get_class($e) == ControllerException::class) {
            /** @var ControllerException $e */
            $description = $e->getDescription();
        }

        $response_data = [
            'errors' => [
                [
                    'status' => (string)$error_code,
                    'title' => (!empty($title) ? $title : $e->getMessage()),
                    'description' => $description,
                    'meta' => [
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ],
                ],
            ],
            'links' => [
                'self' => $request->path(),
            ],
            'jsonapi' => [
                'version' => "1.0",
            ]
        ];
        if (env('APP_ENV') == "local") {
            $response_data['meta'] = [
                'method' => $request->getMethod(),
                'success' => false,
                'request' => $request->all(),
            ];
        }

        if (Str::contains($request->header('accept'), 'msgpack')) {
            /** @noinspection PhpUndefinedFunctionInspection */
            $response = response(msgpack_pack($response_data), $error_code, []);
        } else {
            $response = response()->json($response_data, $error_code, [], JSON_PRETTY_PRINT);
        }

        return $response;
    }
}
