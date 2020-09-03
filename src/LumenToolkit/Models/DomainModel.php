<?php

declare(strict_types=1);

namespace LumenToolkit\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Routing\ProvidesConvenienceMethods;

abstract class DomainModel extends Model
{
    use ProvidesConvenienceMethods;
}
