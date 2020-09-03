<?php

namespace LumenToolkit\Helpers;

use Exception;

class Money
{
    /**
     * @param        $value
     * @param string $symbol
     * @param int    $decimals
     * @param string $decimal_point
     * @param string $thousands_sep
     *
     * @return string
     */
    public static function format($value, $symbol = '$', $decimals = 2, $decimal_point = '.', $thousands_sep = ',')
    {
        return $symbol . number_format((float)$value, $decimals, $decimal_point, $thousands_sep);
    }
}
