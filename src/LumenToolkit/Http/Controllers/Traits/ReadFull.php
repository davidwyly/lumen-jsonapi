<?php 

declare(strict_types=1);

namespace LumenToolkit\Http\Controllers\Traits;

use Laravel\Lumen\Routing\ProvidesConvenienceMethods;
use LumenToolkit\Http\Status;
use Exception;
use \Illuminate\Http\Request;
use \LumenToolkit\Models\DataModel;
use Symfony\Component\HttpFoundation\Response;

trait ReadFull
{
    /**
     * @param Request $request
     * @param         $record_id
     * @param bool    $public
     *
     * @return Response
     */
    public function readFull(Request $request, $record_id, bool $public = false): Response
    {
        try {
            if (empty($record_id)) {
                throw new Exception("Record id cannot be empty", Status::BAD_REQUEST);
            }

            if (!is_numeric($record_id)) {
                throw new Exception("Record id '$record_id' must be numeric", Status::BAD_REQUEST);
            }

            $this->validateModelName();

            if (!$public
                && !$this->isResourceAuthorized($request, $this->model_name, $record_id)
            ) {
                throw new Exception('Resource not authorized', Status::UNAUTHORIZED);
            }

            /** @var DataModel $model */
            $model  = $this->model_name;
            $record = $model::with($model::$api_relations)->findOrFail($record_id);
        } catch (Exception $e) {
            return $this->renderFail($request, $e);
        }

        return $this->renderSuccess($request, $record);
    }
}
