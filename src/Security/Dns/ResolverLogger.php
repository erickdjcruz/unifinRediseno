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

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class ResolverLogger implements Resolver, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private Resolver $resolver;

    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger ?? new NullLogger();
    }

    /**
     * @param string $hostname
     * @return string
     * @throws QueryFailedException
     */
    public function resolveToIp(string $hostname): string
    {
        $this->getLogger()->debug("Resolving $hostname to IP with " . get_class($this->resolver));
        try {
            $ip = $this->resolver->resolveToIp($hostname);
        } catch (QueryFailedException $exception) {
            $this->getLogger()->error("Can't resolve $hostname to IP with " . get_class($this->resolver));
            throw $exception;
        }
        $this->getLogger()->debug("$hostname has been resolved to $ip");
        return $ip;
    }
}
