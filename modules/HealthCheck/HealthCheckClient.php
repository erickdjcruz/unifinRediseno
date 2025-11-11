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

require_once 'include/SugarHttpClient.php';

/**
 * Class HealthCheckClient
 */
class HealthCheckClient extends SugarHttpClient
{
    /**
     * @param $key
     * @param $logFilePath
     * @return bool
     *
     * @deprecated since 7.9
     */
    public function send($key, $logFilePath)
    {
        $GLOBALS['log']->deprecated('sending Log to Sugar is disabled!');
        return false;
    }
}
