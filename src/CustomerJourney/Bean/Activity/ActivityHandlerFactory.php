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

namespace Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity;

use Sugarcrm\Sugarcrm\CustomerJourney\Bean\Activity\Helper\ActivityHelper as ActivityHelper;

/**
 * Responsible for creating AbstractActivityHandler instances
 */
class ActivityHandlerFactory
{
    /**
     * Creates the right ActivityHandler for a given module name
     *
     * @param string $moduleName
     * @return ActivityHelper
     * @throws \InvalidArgumentException
     */
    public static function factory(string $moduleName)
    {
        $moduleName = is_object($moduleName) &&
        $moduleName instanceof \SugarBean ? $moduleName->module_dir : $moduleName;
        return ActivityHelper::getInstance(strtolower($moduleName), $moduleName);
    }

    /**
     * Returns a list of all available AbstractActivityHandler's
     *
     * @return ActivityHelper[]
     */
    public static function all()
    {
        return [
            ActivityHelper::getInstance('meetings', 'Meetings'),
            ActivityHelper::getInstance('tasks', 'Tasks'),
            ActivityHelper::getInstance('calls', 'Calls'),
        ];
    }
}
