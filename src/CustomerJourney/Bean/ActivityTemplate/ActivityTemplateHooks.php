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

namespace Sugarcrm\Sugarcrm\CustomerJourney\Bean\ActivityTemplate;

use Sugarcrm\Sugarcrm\CustomerJourney\LogicHooks\ActivityOrStageTemplateHooksHelper;

class ActivityTemplateHooks
{
    /**
     * All  before_save logic hooks is inside this function.
     *
     * @param object $bean
     * @param string $event
     * @param array $arguments
     */
    public function beforeSave($bean, $event, $arguments)
    {
        ActivityOrStageTemplateHooksHelper::saveFetchedRow($bean);
        ActivityOrStageTemplateHooksHelper::checkAvailableModules($bean, $event, $arguments);
    }
}
