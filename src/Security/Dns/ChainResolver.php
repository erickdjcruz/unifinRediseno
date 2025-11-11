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

final class ChainResolver implements Resolver
{
    /**
     * @var Resolver[]
     */
    private array $resolvers = [];

    public function __construct(Resolver ...$resolvers)
    {
        $this->resolvers = $resolvers;
    }

    public function resolveToIp(string $hostname): string
    {
        foreach ($this->resolvers as $resolver) {
            try {
                return $resolver->resolveToIp($hostname);
            } catch (QueryFailedException $exception) {
            }
        }
        throw new QueryFailedException("Can't resolve $hostname to IP with any resolver");
    }
}
