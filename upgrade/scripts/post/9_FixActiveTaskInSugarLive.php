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
 * Fix Active tasks dashlet on SugarLive Leads tabs
 */
class SugarUpgradeFixActiveTaskInSugarLive extends UpgradeScript
{
    public $order = 9200;
    public $type = self::UPGRADE_DB;

    public function run()
    {
        if (version_compare($this->from_version, '11.2.0', '>=')) {
            return;
        }

        $bean = BeanFactory::newBean('Dashboards');
        $query = new SugarQuery();
        $query->select(['id', 'name', 'metadata']);
        $query->from($bean);
        $query->where()->equals('id', '32bc5cd0-b1a0-11ea-ad16-f45c898a3ce7');
        $results = $query->execute();

        if (!empty($results)) {
            $row = $results[0];
            $updated = false;
            $metadata = json_decode($row['metadata'], true);
            $tabs = $metadata['tabs'];

            foreach ($tabs as $tabNum => $tab) {
                if ($tab['name'] === 'LBL_LEAD') {
                    foreach ($tab['dashlets'] as $dashletNum => $dashlet) {
                        if ($dashlet['view']['name'] === 'active-tasks' && !empty($dashlet['context'])) {
                            unset($dashlet['context']);
                            $metadata['tabs'][$tabNum]['dashlets'][$dashletNum] = $dashlet;
                            $updated = true;
                        }
                    }
                }
            }

            if ($updated) {
                $bean = BeanFactory::retrieveBean('Dashboards', '32bc5cd0-b1a0-11ea-ad16-f45c898a3ce7');
                $bean->metadata = json_encode($metadata);
                $bean->save();
                $this->log('Active tasks dashlet in SugarLive Leads tab has been fixed');
            }
        }
    }
}
