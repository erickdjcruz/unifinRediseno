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

/**
 * Add Change Timers to the hidden sub panels.
 */
class SugarUpgradeAddSTHiddenSubpanel extends UpgradeScript
{
    public $order = 7100;
    public $type = self::UPGRADE_DB;

    public function run()
    {
        if (strtolower($this->to_flavor) === 'ent' &&
            (version_compare($this->from_version, '9.3.0', '<') || strtolower($this->from_flavor) === 'pro')) {
            // Add ChangeTimers to hidden subpanels if it's not already there
            $panels = SubPanelDefinitions::get_hidden_subpanels();
            if (!safeInArray('changetimers', $panels)) {
                $panels['changetimers'] = 'changetimers';
                SubPanelDefinitions::set_hidden_subpanels($panels);
            }
        }
    }
}
