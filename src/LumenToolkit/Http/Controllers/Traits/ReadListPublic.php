<?php

declare(strict_types=1);

namespace LumenToolkit\Http\Controllers\Traits;

use Laravel\Lumen\Routing\ProvidesConvenienceMethods;
use LumenToolkit\Exceptions\ControllerException;
use LumenToolkit\Http\Status;
use Exception;
use \Illuminate\Http\Request;
use \LumenToolkit\Models\DataModel;
use Symfony\Component\HttpFoundation\Response;

trait ReadListPublic
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function readListPublic(Request $request): Response
    {
        try {
            if (empty($this->model_name)) {
                throw new ControllerException("Controller does not have model name defined",
                    Status::INTERNAL_SERVER_ERROR);
            }

            $this->validateModelName();

            /** @var DataModel $model */
            $model   = $this->model_name;
            $records = $model::select()->limit(DataModel::MAX_LIST_RESULTS)->get();
            return $this->renderSuccess($request, $records);
        } catch (Exception $e) {
            return $this->renderFail($request, $e);
        }
    }
}
