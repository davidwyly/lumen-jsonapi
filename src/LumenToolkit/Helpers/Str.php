<?php

namespace LumenToolkit\Helpers;

use Exception;
use Reflection;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;

class Str
{
    /**
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function contains(string $haystack, string $needle): bool
    {
        if (strpos($haystack, $needle) !== false) {
            return true;
        }
        return false;
    }

    /**
     * @param string $string1
     * @param string $string2
     *
     * @return bool
     */
    public static function looselyMatches(string $string1, string $string2): bool
    {
        $string1 = mb_strtolower(trim(self::clean($string1)));
        $string2 = mb_strtolower(trim(self::clean($string2)));
        return ($string1 == $string2);
    }

    /**
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function endsWith(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        return $length === 0 ? true : (substr($haystack, -$length) === $needle);
    }

    /**
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function startsWith(string $haystack, string $needle): bool
    {
        return (strpos($haystack, $needle) === 0);
    }

    /**
     * @param int         $length
     * @param string|null $character_set
     *
     * @return string
     */
    public static function generateRandom(int $length, string $character_set = null): string
    {
        if (is_null($character_set)) {
            $character_set = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        }
        $string           = [];
        $character_length = strlen($character_set);
        for ($i = 0; $i < $length; $i++) {
            $string[] = $character_set[mt_rand(0, $character_length - 1)];
        }
        return implode($string);
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public static function clean(string $text): string
    {
        $utf8 = [
            '/[áàâãªäā]/u' => 'a',
            '/[ÁÀÂÃÄ]/u'   => 'A',
            '/[ÍÌÎÏ]/u'    => 'I',
            '/[íìîï]/u'    => 'i',
            '/[éèêë]/u'    => 'e',
            '/[ÉÈÊË]/u'    => 'E',
            '/[óòôõºö]/u'  => 'o',
            '/[ÓÒÔÕÖ]/u'   => 'O',
            '/[úùûü]/u'    => 'u',
            '/[ÚÙÛÜ]/u'    => 'U',
            '/ç/'          => 'c',
            '/Ç/'          => 'C',
            '/ñ/'          => 'n',
            '/Ñ/'          => 'N',
            '/–/'          => '-', // UTF-8 hyphen to "normal" hyphen
            '/[’‘‹›‚]/u'   => "'", // Literally a single quote
            '/[“”«»„]/u'   => '"', // Double quote
            '/ /'          => ' ', // non-breaking space (equiv. to 0x160)
        ];
        $result = preg_replace(array_keys($utf8), array_values($utf8), $text);
        if (is_null($result)) {
            return $text;
        }
        return $result;
    }
}
