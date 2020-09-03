<?php

namespace LumenToolkit\Database;

use Illuminate\Support\Facades\DB;

class Migration extends \Illuminate\Database\Migrations\Migration
{

    /**
     * Migration constructor.
     */
    public function __construct()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }
}