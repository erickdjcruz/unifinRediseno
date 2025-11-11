<?php

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

declare(strict_types=1);

namespace Sugarcrm\Sugarcrm\Security\ValueObjects;

use Sugarcrm\Sugarcrm\Security\Dns\Resolver;
use Sugarcrm\Sugarcrm\Security\Dns\ResolverFactory;

final class ExternalResource
{
    /** @var string */
    private $ip;

    /** @var string */
    private $host;

    /** @var string */
    private $convertedUrl;

    /**
     * @var string
     */
    private $origUrl;

    private function __construct()
    {
    }

    /**
     * @param string $url
     * @param array $privateIps
     * @return static
     */
    public static function fromString(string $url, array $privateIps = [], ?Resolver $resolver = null): self
    {
        if (false === filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid url was provided.');
        }

        $parts = parse_url($url);
        $host = $parts['host'] ?? null;
        $scheme = $parts['scheme'] ?? null;

        if (empty($host) || !in_array($scheme, ['http', 'https'], true)) {
            throw new \InvalidArgumentException('Invalid url was provided');
        }

        // If host is not IP then resolve it
        if (false === filter_var($host, FILTER_VALIDATE_IP)) {
            $ip = self::resolveToIp($host, $resolver);
        } else {
            $ip = $host;
        }

        if (self::isIpPrivate($ip, $privateIps)) {
            throw new \InvalidArgumentException('The target IP belongs to private network');
        }

        $urlValueObject = new self();
        $urlValueObject->ip = $ip;
        $urlValueObject->host = $host;
        $urlValueObject->convertedUrl = self::buildUrl($parts, $ip);
        $urlValueObject->origUrl = $url;
        return $urlValueObject;
    }

    private static function resolveToIp(string $hostname, ?Resolver $resolver): string
    {
        $resolver = $resolver ?? ResolverFactory::getInstance();
        return $resolver->resolveToIp($hostname);
    }

    /**
     * @param string $ip
     * @param array $privateIps
     * @return bool
     */
    private static function isIpPrivate(string $ip, array $privateIps): bool
    {
        if (empty($privateIps)) {
            return false;
        }

        $longIp = ip2long($ip);

        if ($longIp !== -1) {
            foreach ($privateIps as $privateIp) {
                [$start, $end] = explode('|', $privateIp);

                if ($longIp >= ip2long($start) && $longIp <= ip2long($end)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Resolves a relative URL according to RFC 2396 section 5.2
     * @param string $url
     * @return string
     */
    public function resolveLocation(string $url): string
    {
        $base = $this->origUrl;
        if ($url === '') {
            return $base;
        }
        // already absolute url
        if (preg_match('~^[a-z]+:~i', $url)) {
            return $url;
        }
        $base = parse_url($base);
        if (strpos($url, '#') === 0) {
            $base['fragment'] = substr($url, 1);
            return self::buildUrl($base);
        }
        unset($base['fragment']);
        unset($base['query']);
        if (substr($url, 0, 2) === '//') {
            return self::buildUrl([
                'scheme' => $base['scheme'],
                'path' => substr($url, 2),
            ]);
        }
        if (strpos($url, '/') === 0) {
            $base['path'] = $url;
        } else {
            $path = explode('/', $base['path']);
            $url_path = explode('/', $url);
            array_pop($path);
            $end = array_pop($url_path);
            foreach ($url_path as $segment) {
                if ($segment == '.') {
                    continue;
                }
                if ($segment === '..' && $path && $path[safeCount($path) - 1] !== '..') {
                    array_pop($path);
                } else {
                    $path[] = $segment;
                }
            }
            if ($end === '.') {
                $path[] = '';
            } else {
                if ($end === '..' && $path && $path[safeCount($path) - 1] !== '..') {
                    $path[safeCount($path) - 1] = '';
                } else {
                    $path[] = $end;
                }
            }
            $base['path'] = implode('/', $path);
        }
        return self::buildUrl($base);
    }

    /**
     * @param array $parts
     * @param string|null $ip
     * @return string
     */
    private static function buildUrl(array $parts, string $ip = null): string
    {
        $scheme = isset($parts['scheme']) ? $parts['scheme'] . '://' : '';

        if ($ip === null) {
            $host = $parts['host'] ?? '';
        } else {
            $host = $ip;
        }

        $port = isset($parts['port']) ? ':' . $parts['port'] : '';
        $user = $parts['user'] ?? '';
        $pass = isset($parts['pass']) ? ':' . $parts['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = $parts['path'] ?? '';
        $query = isset($parts['query']) ? '?' . $parts['query'] : '';
        $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';

        return $scheme . $user . $pass . $host . $port . $path . $query . $fragment;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getConvertedUrl(): string
    {
        return $this->convertedUrl;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }
}
