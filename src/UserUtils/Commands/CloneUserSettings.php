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

namespace Sugarcrm\Sugarcrm\UserUtils\Commands;

use Sugarcrm\Sugarcrm\UserUtils\Command;
use Sugarcrm\Sugarcrm\UserUtils\Constants\CommandType;

class CloneUserSettings extends Command
{
    /**
     * The command type
     *
     * @var string
     */
    public $type = CommandType::CloneUserSettings;

    /**
     * Perform cloning user settings
     */
    public function execute(): void
    {
        $manager = $this->getManager();
        $manager->cloneUserSettings();
    }
}
