<?php

namespace LumenToolkit\Helpers;

use Exception;
use Reflection;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;

class Math
{
    /**
     * @param float|int ...$values
     *
     * @return float|int
     */
    public static function median(...$values)
    {
        sort($values);
        $count        = count($values);
        $middle_value = (int)floor(($count - 1) / 2);
        if ($count % 2) {
            $median = $values[$middle_value];
        } else {
            $low    = $values[$middle_value];
            $high   = $values[$middle_value + 1];
            $median = (($low + $high) / 2);
        }
        return $median;
    }

    /**
     * @param int  $min_digits
     * @param int  $max_digits
     * @param bool $signed
     *
     * @return int
     * @throws Exception
     */
    public static function randomInteger(int $min_digits, int $max_digits, $signed = false)
    {
        if ($min_digits > $max_digits) {
            throw new Exception("Minimum digits cannot be greater than maximum digits");
        }
        $min   = pow(10, $min_digits - 1);
        $max   = pow(10, $max_digits) - 1;
        $value = mt_rand($min, $max);
        if ($signed && mt_rand(0, 1) === 0) {
            return $value * -1;
        }
        return $value;
    }

    /**
     * @param int  $min_digits
     * @param int  $max_digits
     * @param bool $signed
     *
     * @return string
     * @throws Exception
     */
    public static function randomIntegerLeadingZeros(int $min_digits, int $max_digits, $signed = false)
    {
        $value = (string)self::randomInteger($min_digits, $max_digits);
        $value = str_pad($value, $max_digits, '0', STR_PAD_LEFT);
        if ($signed && mt_rand(0, 1) === 0) {
            return '-' . $value;
        }
        return $value;
    }

    /**
     * @param int  $min_prefix_digits
     * @param int  $max_prefix_digits
     * @param int  $min_suffix_digits
     * @param int  $max_suffix_digits
     * @param bool $signed
     *
     * @return float
     * @throws Exception
     */
    public static function randomFloat(
        int $min_prefix_digits,
        int $max_prefix_digits,
        int $min_suffix_digits,
        int $max_suffix_digits,
        $signed = false
    ): float {
        $prefix = self::randomInteger($min_prefix_digits, $max_prefix_digits);
        $suffix = self::randomIntegerLeadingZeros($min_suffix_digits, $max_suffix_digits);
        $value = $prefix . '.' . $suffix;
        $value = (float)$value;
        if ($signed && mt_rand(0, 1) === 0) {
            return $value * -1;
        }
        return $value;
    }
}
