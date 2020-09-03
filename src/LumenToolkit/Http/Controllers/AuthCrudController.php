<?php

declare(strict_types=1);

namespace LumenToolkit\Http\Controllers;

use LumenToolkit\Http\Controllers\Traits;
use Laravel\Lumen\Routing\Controller as BaseController;

abstract class AuthCrudController extends AuthController
{
    use Traits\Create;
    use Traits\Read;
    use Traits\Update;
    use Traits\Delete;
}
