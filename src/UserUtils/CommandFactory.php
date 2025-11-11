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

namespace Sugarcrm\Sugarcrm\UserUtils;

use Sugarcrm\Sugarcrm\UserUtils\Command;
use Sugarcrm\Sugarcrm\UserUtils\Invoker\InvokerPayload;

/**
 * Factory class to get a command type
 */
class CommandFactory
{
    /**
     * Get a command class based on command type
     *
     * @param CommandType $type
     * @return string the command class
     */
    public static function getCommand(string $type, InvokerPayload $payload): Command
    {
        $log = \LoggerManager::getLogger();
        $commandClass = __NAMESPACE__ . '\Commands\\' . $type;

        if (class_exists($commandClass)) {
            try {
                $instance = new $commandClass($payload);
                return $instance;
            } catch (\Throwable $t) {
                $log->error('UserUtils: ' . $t->getMessage());
            }
        } else {
            $log->error('UserUtils: command class ' . $commandClass . ' doesn\'t exist.');
        }

        return null;
    }
}
