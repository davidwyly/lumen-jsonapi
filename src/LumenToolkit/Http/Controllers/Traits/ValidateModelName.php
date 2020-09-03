<?php

declare(strict_types=1);

namespace LumenToolkit\Http\Controllers\Traits;

use LumenToolkit\Http\Status;
use LumenToolkit\Http\Controllers\Controller;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use LumenToolkit\Models\DataModel;
use LumenToolkit\Models\User;

trait ValidateModelName {
    /**
     * @var string|DataModel
     */
    protected string $model_name;

    /**
     * @throws Exception
     */
    protected function validateModelName(): void
    {
        if (empty($this->model_name)) {
            $current_model = get_called_class();
            throw new Exception("Data controller '$current_model' is missing required property 'model_name'",
                                Status::INTERNAL_SERVER_ERROR);
        }
    }
}

