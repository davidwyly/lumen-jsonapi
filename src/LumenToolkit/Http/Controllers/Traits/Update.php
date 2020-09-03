<?php

declare(strict_types=1);

namespace LumenToolkit\Http\Controllers\Traits;

use Laravel\Lumen\Routing\ProvidesConvenienceMethods;
use LumenToolkit\Exceptions\ControllerException;
use LumenToolkit\Http\Status;
use LumenToolkit\Models\DataModel;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use \Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

trait Update
{
    /**
     * @param Request $request
     * @param         $record_id
     *
     * @return Response
     */
    public function update(Request $request, $record_id): Response
    {
        try {
            $this->validateModelName();
            $model = $this->model_name;

            if (!$this->isResourceAuthorized($request, $this->model_name, $record_id)) {
                throw new Exception('Resource not authorized', Status::UNAUTHORIZED);
            }

            $update_rules       = [];
            $model_rules        = $model::$rules;
            $request_attributes = $request->all();

            if (count($request_attributes) < 1) {
                throw new Exception("No attributes to update", Status::BAD_REQUEST);
            }

            $bad_keys = [];
            foreach ($request_attributes as $update_key => $update_value) {
                if (!array_key_exists($update_key, $model_rules)) {
                    $bad_keys[$update_key] = [
                        "No model rules definition exists for this attribute",
                    ];
                    continue;
                }
                $update_rules[$update_key] = $model_rules[$update_key];
            }
            if (!empty($bad_keys)) {
                throw new ControllerException("Extraneous attributes", Status::BAD_REQUEST, $bad_keys);
            }

            $this->validate($request, $update_rules);

            /** @var DataModel $model */
            /** @var Builder|DataModel $record */
            $record = $model::findOrFail($record_id);
            foreach ($request->all() as $key => $value) {

                $record->{$key} = ($value == "") ? null : $value;
            }

            $record->save();
        } catch (Exception $e) {
            return $this->renderFail($request, $e);
        }

        return $this->renderSuccess($request, ['updated_record_id' => $record->id], Status::OK);
    }
}
