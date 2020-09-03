<?php

namespace LumenToolkit\Helpers;

use Exception;

class Map
{
    /**
     * @param $array
     * @param $callable
     *
     * @return array
     * @throws Exception
     */
    public static function dictMap($array, $callable)
    {
        $new_array = [];

        foreach ($array as $key => $value) {
            $new_array[] = is_callable($callable) ? $callable($key, $value)
                : self::resolveCallable($callable, [$key, $value]);
        }

        return $new_array;
    }

    /**
     * @param $callable
     * @param $args
     *
     * @return mixed
     * @throws Exception
     */
    public static function resolveCallable($callable, $args)
    {
        if (!is_array($args)) {
            $args = [$args];
        }

        if (!is_callable($callable)) {
            throw new Exception("Failed to resolve callable function");
        }

        return call_user_func_array($callable, $args);
    }
}