<?php

declare(strict_types=1);

namespace LumenToolkit\Http\Controllers\Traits;

use Laravel\Lumen\Routing\ProvidesConvenienceMethods;
use LumenToolkit\Http\Status;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use \Illuminate\Http\Request;
use \LumenToolkit\Models\DataModel;
use Symfony\Component\HttpFoundation\Response;

trait Read
{
    /**
     * @param Request $request
     * @param         $record_id
     * @param bool    $public
     *
     * @return Response
     */
    public function read(Request $request, $record_id, bool $public = false): Response
    {
        try {
            $this->validateModelName();
            $model = $this->model_name;

            if (!$public
                && !$this->isResourceAuthorized($request, $model, $record_id)
            ) {
                throw new Exception('Resource not authorized', Status::UNAUTHORIZED);
            }

            /** @var DataModel $model */
            /** @var Builder|DataModel $record */
            $record = $model::whereKey($record_id)->first();
        } catch (Exception $e) {
            return $this->renderFail($request, $e);
        }

        return $this->renderSuccess($request, $record);
    }
}
