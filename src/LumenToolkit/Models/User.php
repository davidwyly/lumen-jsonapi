<?php

declare(strict_types=1);

namespace LumenToolkit\Models;

use Brick\Math\BigInteger;
use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

/**
 * @property BigInteger                 $id
 * @property string                     $cognito_guid
 */
class User extends DataModel implements AuthenticatableContract
{
    use SoftDeletes, Authenticatable, Authorizable;

    public const TABLE = 'users';

    protected array $hidden = ['cognito_guid'];
}
