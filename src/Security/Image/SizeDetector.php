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

namespace Sugarcrm\Sugarcrm\Security\Image;

use Sugarcrm\Sugarcrm\Security\ValueObjects\ExternalResource;

class SizeDetector
{
    private array $allowedDomains = [];
    private ?string $error = null;

    public function __construct(array $allowedDomains = [])
    {
        $this->allowedDomains = $allowedDomains;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @param string $path
     * @return array|false
     */
    public function getSize(string $path)
    {
        if (stream_is_local($path)) {
            return [$width, $height] = $this->getImageInfo($path);
        }
        $parts = parse_url($path);
        $domain = $parts['host'] ?? null;
        $scheme = $parts['scheme'] ?? null;
        $port = $parts['port'] ?? null;
        if ($port !== null) {// Ports are not allowed
            $this->error = 'Ports are not allowed';
            return false;
        }

        if (in_array($domain, $this->allowedDomains) || ($scheme === null)) {
            return [$width, $height] = $this->getImageInfo($path);
        }

        $tempFile = $this->generateTmpFileName();
        $context = $this->createContext($path);

        if ($this->download($path, $tempFile, $context)) {
            $size = $this->getImageInfo($tempFile);
            unlink($tempFile);
            return false === $size? false : [$size[0], $size[1]];
        } else {
            unlink($tempFile);
            return false;
        }
    }

    /**
     * @param array $headers array $headers Request headers list in a form of key + value pairs, e.g.
     *  ['Content-type' => 'application/x-www-form-urlencoded']
     * @param ExternalResource $externalResource
     * @return string[]
     */
    protected function buildHeaders(array $headers, ExternalResource $externalResource): array
    {
        $requestHeaders = [];
        foreach ($headers as $name => $value) {
            $canonicalName = strtolower(trim($name));
            $requestHeaders[$canonicalName] = $value;
        }
        // force correct Host header, override if it was provided in $headers
        $requestHeaders['host'] = $externalResource->getHost();
        return array_map(function ($k, $v) {
            return "$k: $v";
        }, array_keys($requestHeaders), array_values($requestHeaders));
    }

    /**
     * @param string $path
     * @return resource
     */
    protected function createContext(string $path)
    {
        $headers = [];
        $externalResource = $this->createExternalResource($path, []);

        $headerParam = $this->buildHeaders($headers, $externalResource);

        $options = [
            'http' => [
                'method' => 'GET',
                'header' => $headerParam,
                'follow_location' => 0, // mitigate SSRF via redirect
                'content' => null,
                'timeout' => 3,
                'protocol_version' => 1.1,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => false,
                'peer_name' => $externalResource->getHost(),
            ],
        ];

        return stream_context_create(
            $options
        );
    }

    /**
     * @param string $path
     * @param array $privateIps
     * @return ExternalResource
     */
    protected function createExternalResource(string $path, array $privateIps): ExternalResource
    {
        return ExternalResource::fromString($path, $privateIps);
    }

    /**
     * @param string $path
     * @param string $tempFile
     * @param $context
     * @return bool
     */
    protected function download(string $path, string $tempFile, $context): bool
    {
        $level = error_reporting(0);
        if (!$res = copy($path, $tempFile, $context)) {
            $this->error = 'Can not download image';
        }
        error_reporting($level);
        return $res;
    }

    /**
     * @param string $path
     * @return array|false
     */
    protected function getImageInfo(string $path)
    {
        $level = error_reporting(0);
        $size = getimagesize($path);
        if ($size === false) {
            $this->error = 'Can not get image size';
        }
        error_reporting($level);
        return $size;
    }

    /**
     * @return false|string
     */
    protected function generateTmpFileName()
    {
        return tempnam(sys_get_temp_dir(), 'image_');
    }
}
