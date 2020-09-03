<?php

namespace LumenToolkit\Exceptions;

use Exception;

class ControllerException extends Exception
{
    private $description = null;

    public function __construct($message, $code, $description = null)
    {
        parent::__construct($message, $code);
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }
}
