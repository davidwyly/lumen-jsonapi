<?php

declare(strict_types=1);

namespace LumenToolkit\Http\Controllers\Traits;

use Laravel\Lumen\Routing\ProvidesConvenienceMethods;
use LumenToolkit\Http\Status;
use LumenToolkit\Models\DataModel;
use Illuminate\Database\Eloquent\Builder;
use \Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

trait Create
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $this->validateModelName();
            $model = $this->model_name;

            $this->validate($request, $model::$rules);

            /** @var DataModel $model */
            /** @var Builder|DataModel $record */
            $record = new $model();
            foreach ($request->all() as $key => $value) {
                if ($key == 'payload' || $key == 'user_id') {
                    continue;
                }
                $record->{$key} = ($value == "") ? null : $value;
            }

            $record->save();
        } catch (\Exception $e) {
            return $this->renderFail($request, $e);
        }

        return $this->renderSuccess($request, ['created_record_id' => $record->id], Status::CREATED);
    }
}
