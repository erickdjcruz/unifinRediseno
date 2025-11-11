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

class TimelineConfigApiHandler extends ConfigApiHandler
{
    /**
     * Saves new configuration for timeline and returns updated config
     *
     * @param ServiceBase $api The RestService object
     * @param array $args Arguments passed to the service
     * @return array
     */
    public function setConfig(ServiceBase $api, array $args): array
    {
        $result = parent::setConfig($api, $args);
        self::clearCache();
        return $result;
    }
}
