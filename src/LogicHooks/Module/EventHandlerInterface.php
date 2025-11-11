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

namespace Sugarcrm\Sugarcrm\LogicHooks\Module;

use SugarBean;

interface EventHandlerInterface
{
    /**
     * Handles a module event.
     *
     * @param SugarBean $bean The impacted record.
     * @param string $event The event type.
     * @param array $args Additional arguments.
     *
     * @return void
     */
    public function handleEvent(SugarBean $bean, string $event, array $args): void;
}
