<?php

namespace LumenToolkit\Helpers;

class Ip
{
    /**
     * Detect IPv4 ip addresses
     *
     * @param $ip
     *
     * @return bool
     */
    public static function isIpv4($ip): bool
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return true;
        }
        return false;
    }

    /**
     * Detect IPv6 ip addresses
     *
     * @param $ip
     *
     * @return bool
     */
    public static function isIpv6($ip): bool
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return true;
        }
        return false;
    }
}