<?php 

declare(strict_types=1);

namespace LumenToolkit\Http\Controllers\Traits;

use Laravel\Lumen\Routing\ProvidesConvenienceMethods;
use LumenToolkit\Http\Status;
use LumenToolkit\Models\DataModel;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use \Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

trait Delete
{
    /**
     * @param Request $request
     * @param         $record_id
     *
     * @return Response
     */
    public function delete(Request $request, $record_id): Response
    {
        try {
            $this->validateModelName();

            if (!$this->isResourceAuthorized($request, $this->model_name, $record_id)) {
                throw new Exception('Resource not authorized', Status::UNAUTHORIZED);
            }

            $model  = $this->model_name;
            /** @var DataModel $model */
            /** @var Builder|DataModel $record */
            $record = $model::whereKey($record_id)->first();
            if (empty($record)) {
                // idempotency check; if the record can't be found, deletion is assumed to be successful
                return $this->renderIdempotent($request, ['deleted_record_id' => $record_id], Status::OK);
            }
            $record->delete();
        } catch (Exception $e) {
            return $this->renderFail($request, $e);
        }

        return $this->renderSuccess($request, ['deleted_record_id' => $record->id], Status::OK);
    }
}
