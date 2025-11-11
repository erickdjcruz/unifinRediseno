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

final class HeaderValue
{
    /**
     * No public constructor.
     */
    private function __construct()
    {
    }

    /**
     * Filter the header value according to RFC 2822
     *
     * @see    http://www.rfc-base.org/txt/rfc-2822.txt (section 2.2)
     * @param string $value
     * @return string
     */
    public static function filter($value)
    {
        $result = '';
        $total = strlen($value);

        // Filter for CR and LF characters, leaving CRLF + WSP sequences for
        // Long Header Fields (section 2.2.3 of RFC 2822)
        for ($i = 0; $i < $total; $i += 1) {
            $ord = ord($value[$i]);
            if ($ord === 10 || $ord > 127) {
                continue;
            }

            if ($ord === 13) {
                if ($i + 2 >= $total) {
                    continue;
                }

                $lf = ord($value[$i + 1]);
                $sp = ord($value[$i + 2]);

                if ($lf !== 10 || $sp !== 32) {
                    continue;
                }

                $result .= "\r\n ";
                $i += 2;
                continue;
            }

            $result .= $value[$i];
        }

        return $result;
    }

    /**
     * Determine if the header value contains any invalid characters.
     *
     * @see    http://www.rfc-base.org/txt/rfc-2822.txt (section 2.2)
     * @param string $value
     * @return bool
     */
    public static function isValid($value)
    {
        // SS-634: Disable character validation, which causes errors on valid email imports
        return true;
    }

    /**
     * Assert that the header value is valid.
     *
     * Raises an exception if invalid.
     *
     * @param string $value
     * @return void
     * @throws Exception\RuntimeException
     */
    public static function assertValid($value)
    {
        if (!self::isValid($value)) {
            throw new Exception\RuntimeException('Invalid header value detected');
        }
    }
}
