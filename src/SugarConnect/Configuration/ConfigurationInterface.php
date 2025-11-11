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

namespace Sugarcrm\Sugarcrm\SugarConnect\Configuration;

use Sugarcrm\Sugarcrm\SugarConnect\Client\Client;

/**
 * @deprecated Will be removed in the next release.
 */
interface ConfigurationInterface
{
    /**
     * Tells whether or not Sugar Connect is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Returns the Sugar Connect webhook client.
     *
     * @return Client
     */
    public function getClient(): Client;
}
