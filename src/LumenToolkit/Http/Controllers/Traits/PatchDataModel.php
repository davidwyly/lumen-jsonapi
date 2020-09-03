<?php

declare(strict_types=1);

namespace LumenToolkit\Http\Controllers\Traits;

use Laravel\Lumen\Routing\ProvidesConvenienceMethods;
use LumenToolkit\Http\Status;
use LumenToolkit\Models\DataModel;
use Exception;
use Illuminate\Http\Request;

trait PatchDataModel
{
    use ValidateModelName;
    use ProvidesConvenienceMethods;

    /**
     * @param Request $request
     * @param string  $data_model_class
     * @param         $model_id
     * @param array   $disallowed_fields
     *
     * @return DataModel
     * @throws Exception
     */
    protected function patchDataModel(
        Request $request,
        string $data_model_class,
        $model_id,
        array $disallowed_fields = []
    ): object {
        /** @var DataModel $data_model_class */
        if (!class_exists($data_model_class)) {
            throw new Exception("Data model class '$data_model_class'' not found", Status::INTERNAL_SERVER_ERROR);
        }

        $request_all = $request->all();
        if (isset($request_all['payload'])) {
            unset($request_all['payload']);
        }
        if (isset($request_all['user_id'])) {
            unset($request_all['user_id']);
        }
        $request_fields = array_keys($request_all);

        if (empty($request_fields)) {
            throw new Exception("Empty payload", Status::BAD_REQUEST);
        }

        $disallowed_field_errors = [];
        foreach ($disallowed_fields as $disallowed_field) {
            if (in_array($disallowed_field, $request_fields)) {
                $disallowed_field_errors[] = $disallowed_field;
            }
        }
        if (!empty($disallowed_field_errors)) {
            throw new Exception("Disallowed fields: " . implode(',', $disallowed_field_errors),
                Status::BAD_REQUEST);
        }

        /** @var DataModel $model */
        $model      = $data_model_class::findOrFail($model_id);
        error_log(print_r($model->getAttributes(), true));
        $key_fields = array_merge($model->getAttributes(), $model->getMutatedAttributes());

        $allowed_field_errors = [];
        $allowed_fields       = array_keys($key_fields);
        foreach ($request_fields as $request_field) {
            if (!in_array($request_field, $allowed_fields)) {
                $allowed_field_errors[] = $request_field;
            }
        }
        if (!empty($allowed_field_errors)) {
            throw new Exception("Unknown fields: " . implode(',', $allowed_field_errors), Status::BAD_REQUEST);
        }

        $unset_fields = [
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
        $unset_fields = array_merge($unset_fields, $disallowed_fields);
        foreach ($unset_fields as $unset_field) {
            unset($key_fields[$unset_field]);
        }

        $fields = array_keys($key_fields);

        $rules = [];
        foreach ($fields as $field) {
            $required_without_all = $fields;
            unset($required_without_all[$field]);
            $required_without_all_rule = implode(',', $required_without_all);
            $rules[$field]             = "required_without_all:$required_without_all_rule";
        }

        $this->validate($request, $rules);

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $model->{$field} = $request->input($field);
            }
        }

        $model->save();

        return $model;
    }
}
