<?php

/**
 * @see       https://github.com/laminas/laminas-mail for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mail/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mail/blob/master/LICENSE.md New BSD License
 */

/**
 * SugarCRM Changelog
 * 06/08/2020 Changed isValid function to remove character validation
 */

namespace Laminas\Mail\Header;

final class HeaderName
{
    /**
     * No public constructor.
     */
    private function __construct()
    {
    }

    /**
     * Filter the header name according to RFC 2822
     *
     * @see    http://www.rfc-base.org/txt/rfc-2822.txt (section 2.2)
     * @param string $name
     * @return string
     */
    public static function filter($name)
    {
        $result = '';
        $tot = strlen($name);
        for ($i = 0; $i < $tot; $i += 1) {
            $ord = ord($name[$i]);
            if ($ord > 32 && $ord < 127 && $ord !== 58) {
                $result .= $name[$i];
            }
        }
        return $result;
    }

    /**
     * Determine if the header name contains any invalid characters.
     *
     * @param string $name
     * @return bool
     */
    public static function isValid($name)
    {
        // SS-634: Disable character validation, which causes errors on valid email imports
        return true;
    }

    /**
     * Assert that the header name is valid.
     *
     * Raises an exception if invalid.
     *
     * @param string $name
     * @return void
     * @throws Exception\RuntimeException
     */
    public static function assertValid($name)
    {
        if (!self::isValid($name)) {
            throw new Exception\RuntimeException('Invalid header name detected');
        }
    }
}
