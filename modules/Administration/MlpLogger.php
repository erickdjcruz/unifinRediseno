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

class MlpLogger extends SugarLogger
{
    public static function replaceDefault()
    {
        $GLOBALS['sugar_config']['logger']['file']['name'] = 'package_install';
        SugarConfig::getInstance()->clearCache();
        LoggerManager::getLogger()->setLevel('debug');
        LoggerManager::setLogger('default', 'MlpLogger');
    }

    public function __construct()
    {
        parent::__construct();
        LoggerManager::setLogger('default', 'MlpLogger');
    }
}
