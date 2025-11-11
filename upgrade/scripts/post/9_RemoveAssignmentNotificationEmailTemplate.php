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

class SugarUpgradeRemoveAssignmentNotificationEmailTemplate extends UpgradeScript
{
    public $order = 9100;
    public $type = self::UPGRADE_DB;

    public function run()
    {
        if (version_compare($this->from_version, '12.1.0', '<')) {
            $emailConfig = SugarConfig::getInstance()->get('emailTemplate');
            $templateID = $emailConfig['AssignmentNotification'] ?? '';
            $emailTemplate = BeanFactory::retrieveBean('EmailTemplates', $templateID);
            if ($emailTemplate) {
                $emailTemplate->mark_deleted($templateID);
                $emailTemplate->save();
            }
        }
    }
}
