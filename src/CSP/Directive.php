<?php

declare(strict_types=1);
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

namespace Sugarcrm\Sugarcrm\CSP;

use Assert\Assertion;

final class Directive
{
    private $name;
    private $srcValue;
    private static $regex = <<<'regex_WRAP'
    ~
        (?(DEFINE)
           (?<ipv4>                                         # IPv4 address / domain name (with sub-domain wildcards)
              (?=\S*?(?:\.|localhost))                      # make sure there is at least one dot or localhost
              (?:\*\.)?                                     # wildcard only allowed at start
              (?:[a-z\d-][a-z\d.-]*|%[a-f\d]{2}+)
           )
           (?<ipv6>\[(?:[a-f\d]{0,4}:)*(?:[a-f\d]{0,4})\])  # IPv6 address
           (?<port>:\d+)                                    # port number
           (?<schemeSource>                                   # data: http: https: blob:
              (?<!.)(data:|http:|https:|blob:)(?!.)
           )
           (?<wildcard>                                     # wildcard
              (?<!.)\*(?!.)
           )
           (?<httpScheme>https?://)
           (?<websocketScheme>wss?://)
           (?<url>                                          # host
              (?&httpScheme)?
              (?&websocketScheme)?
              (?:(?&ipv4)|(?&ipv6))
              (?&port)?                                     # optional port number
           )
           (?<keyword>('unsafe-eval'|'unsafe-inline'|'unsafe-hashes'|'self'))
        )
        ^(?:(?&url)|(?&schemeSource)|(?&wildcard)|(?&keyword))$           # regex
    ~ix
regex_WRAP;
    private $hidden = false;

    private function __construct()
    {
    }

    public static function create(string $name, string $source): Directive
    {
        $sanitizedSrcValue = self::sanitizeSrcValue($source);
        self::assertDirectiveName($name);

        Assertion::allRegex(explode(' ', $sanitizedSrcValue), self::$regex, sprintf('Invalid CSP src value "%s"', $source));

        $directive = new self();
        $directive->name = $name;
        $directive->srcValue = $sanitizedSrcValue;
        return $directive;
    }

    public static function createHidden(string $name, string $source): Directive
    {
        $directive = self::create($name, $source);
        $directive->hidden = true;
        return $directive;
    }

    public static function createWithEmptySource(string $name): Directive
    {
        self::assertDirectiveName($name);
        $directive = new self();
        $directive->name = $name;
        $directive->srcValue = '';
        return $directive;
    }

    public static function createHiddenWithEmptySource(string $name): Directive
    {
        $directive = self::createWithEmptySource($name);
        $directive->hidden = true;
        return $directive;
    }

    public static function isValidSrcValue(string $value): bool
    {
        return (bool)preg_match(self::$regex, $value);
    }

    public static function sanitizeSrcValue(string $value): string
    {
        $normalizedDirective = str_replace(['  ', ';'], [' ', ''], htmlspecialchars_decode(strtolower($value), ENT_QUOTES));
        $values = explode(' ', $normalizedDirective);
        $uniqueValues = array_unique($values);
        return implode(' ', $uniqueValues);
    }

    /**
     * @param string $name
     */
    private static function assertDirectiveName(string $name): void
    {
        Assertion::inArray($name, [
            'default-src',
            'style-src',
            'script-src',
            'img-src',
            'base-uri',
            'form-action',
            'frame-src',
            'child-src',
            'connect-src',
            'font-src',
            'frame-ancestors',
            'object-src',
        ]);
    }

    public function value(): string
    {
        return $this->name . ' ' . $this->srcValue;
    }

    public function source(): string
    {
        return $this->srcValue;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }
}
