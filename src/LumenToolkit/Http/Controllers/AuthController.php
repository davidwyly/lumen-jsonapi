<?php

declare(strict_types=1);

namespace LumenToolkit\Http\Controllers;

use LumenToolkit\Http\Controllers\Traits;
use Laravel\Lumen\Routing\Controller as BaseController;

abstract class AuthController extends BaseController
{
    use Traits\Render;
    use Traits\CognitoAuth;
    use Traits\ValidateModelName;
}
