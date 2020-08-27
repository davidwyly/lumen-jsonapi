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

    /**
     * @param float  $value
     * @param string $currency_code
     *
     * @return Money
     * @throws Exception
     */
    public static function float(float $value, string $currency_code): Money
    {

    }

    public function __construct(int $cents, $currency_code)
    {
    }
}
