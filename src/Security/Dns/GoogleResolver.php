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

namespace Sugarcrm\Sugarcrm\Security\Dns;

final class GoogleResolver implements Resolver
{
    public const DEFAULT_OPTIONS = [
        'http' => [
            'header' => ['Host: dns.google'],
            'follow_location' => 1,
            'timeout' => 2,
            'ignore_errors' => true,
        ],
        'ssl' => [
            'peer_name' => 'dns.google',
        ],
    ];

    private array $options = [];

    public function __construct(array $options = self::DEFAULT_OPTIONS)
    {
        $this->options = $options;
    }

    public function resolveToIp(string $hostname): string
    {
        $context = stream_context_create($this->options);
        $response = file_get_contents('https://8.8.4.4/resolve?' . http_build_query([
                'name' => $hostname,
                'type' => 1,
            ]), false, $context);
        $result = (array)json_decode((string)$response, true);
        $answer = $result['Answer'][0]['data'] ?? null;
        if (null === $answer) {
            throw new QueryFailedException("Can't resolve $hostname to IP");
        }
        return $answer;
    }
}
