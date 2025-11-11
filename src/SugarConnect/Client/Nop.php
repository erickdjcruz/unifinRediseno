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

namespace Sugarcrm\Sugarcrm\SugarConnect\Client;

/**
 * @deprecated Will be removed in the next release.
 */
final class Nop implements Client
{
    /**
     * Logs, at the DEBUG level, the JSON that would be sent to the Sugar
     * Connect webhook.
     *
     * @param array $events The events to send to the webhook.
     *
     * @return void
     */
    public function send(array $events): void
    {
        $log = \LoggerManager::getLogger();
        $log->debug('sugar connect: client: post: ' . json_encode($events));
    }
}
