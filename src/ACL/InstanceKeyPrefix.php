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

namespace Sugarcrm\Sugarcrm\ACL;

final class InstanceKeyPrefix implements KeyConverter
{
    /**
     * Application instance key
     *
     * @var string
     */
    private $instanceKey;

    public function __construct(string $instanceKey)
    {
        $this->instanceKey = $instanceKey;
    }

    public function convert(string $key): string
    {
        return $this->instanceKey . '_' . $key;
    }
}
