<?php

namespace LumenToolkit\Helpers;

use LumenToolkit\Exceptions\InvalidCallableException;
use Exception;
use Reflection;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;

class Parse
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
     * @param string|null $characters
     *
     * @return string
     */
    public static function getRandom(int $length, string $characters = null): string
    {
        if (is_null($characters)) {
            $characters = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        }
        $string           = [];
        $character_length = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $string[] = $characters[mt_rand(0, $character_length - 1)];
        }
        return implode($string);
    }

    /**
     * @param string $text
     *
     * @return string
     * @throws Exception
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
            throw new Exception("Replace operation failed");
        }
        return $result;
    }
}
