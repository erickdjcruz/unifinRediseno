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

use Sugarcrm\Sugarcrm\Security\Password\Utilities;

class SugarUpgradeAddPortalForgotPwdEmailTemplate extends UpgradeScript
{
    public $order = 9018;
    public $type = self::UPGRADE_DB;

    public function run()
    {
        if (strtolower($this->to_flavor) === 'ent' && (version_compare($this->from_version, '9.2.0', '<') ||
                strtolower($this->from_flavor) === 'pro')) {
            // prepare the template
            $team = BeanFactory::getBean('Teams');
            $teamId = $team->retrieve_team_id('Administrator');

            global $mod_strings;

            // Add the "Portal Forgot Password Email" template
            $id = Utilities::addPortalPasswordSeedData($teamId, $mod_strings, 'lostpasswordtmpl');
            $this->upgrader->config['portalpasswordsetting']['lostpasswordtmpl'] = $id;

            // Add the "Portal Password Reset Confirmation Email" template
            $id = Utilities::addPortalPasswordSeedData($teamId, $mod_strings, 'resetpasswordtmpl');
            $this->upgrader->config['portalpasswordsetting']['resetpasswordtmpl'] = $id;
        }
    }
}
