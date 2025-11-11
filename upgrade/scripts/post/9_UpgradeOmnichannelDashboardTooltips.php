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
 * Update tooltips for omnichannel dashboard
 */
class SugarUpgradeUpgradeOmnichannelDashboardTooltips extends UpgradeScript
{
    public $order = 9600;
    public $type = self::UPGRADE_DB;
    public const OMNICHANNEL_DASHBOARD_ID = 'c290ef46-7606-11e9-9129-f218983a1c3e';

    /**
     * Execute upgrade tasks
     * @see UpgradeScript::run()
     */
    public function run()
    {
        $this->log('Updating tooltips for omnichannel dashboard ...');
        if (version_compare($this->from_version, '14.0.0', '>=')) {
            return;
        }

        $bean = BeanFactory::getBean('Dashboards', self::OMNICHANNEL_DASHBOARD_ID);
        $metadata = json_decode($bean->metadata, true);
        if ($this->upgradeDashletMeta($metadata)) {
            $bean->metadata = json_encode($metadata);
            $bean->save();
            $this->log('Done');
        }
    }

    /**
     * @param array $metadata
     * @return bool
     */
    public function upgradeDashletMeta(&$metadata): bool
    {
        $dashletsMeta = &$metadata['dashlets'];
        if (!$dashletsMeta) {
            return false;
        }

        foreach ($dashletsMeta as &$dashletMeta) {
            if (($dashletMeta['view']['type'] ?? '') === 'activity-timeline') {
                $buttonsMeta = &$dashletMeta['view']['custom_toolbar']['buttons'];
                if (!$buttonsMeta) {
                    continue;
                }
                foreach ($buttonsMeta as &$buttonMeta) {
                    if (($buttonMeta['type'] ?? '') === 'actiondropdown') {
                        $buttonMeta['tooltip'] = 'LBL_CREATE_BUTTON_LABEL';
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
