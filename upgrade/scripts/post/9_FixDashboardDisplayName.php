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
 * Fix display name missing from home, notes, tasks and products dashboards*
 */
class SugarUpgradeFixDashboardDisplayName extends UpgradeScript
{
    public $order = 9200;
    public $type = self::UPGRADE_DB;

    public function run()
    {
        /**
         * These dashboards have metadata stored in DB dashboards table. Missing name issue has been fixed
         * on 9.1.0 for new installs. We need to make sure they are reflected to instances from upgrade too.
         */
        if (version_compare($this->from_version, '10.0.0', '>=')) {
            return;
        }

        $dashboardNames = [
            'LBL_HOME_DASHBOARD',
            'LBL_NOTES_LIST_DASHBOARD',
            'LBL_QUOTED_LINE_ITEMS_LIST_DASHBOARD',
            'LBL_TASKS_LIST_DASHBOARD',
        ];

        $bean = BeanFactory::newBean('Dashboards');
        $query = new SugarQuery();
        $query->select(['id', 'name', 'metadata']);
        $query->from($bean);
        $query->where()->in('name', $dashboardNames);
        $rows = $query->execute();

        foreach ($rows as $row) {
            $updated = false;
            $metadata = json_decode($row['metadata'], true);

            switch ($row['name']) {
                case 'LBL_HOME_DASHBOARD':
                case 'LBL_NOTES_LIST_DASHBOARD':
                case 'LBL_QUOTED_LINE_ITEMS_LIST_DASHBOARD':
                    if (!empty($metadata['components'][0]['rows'][1][0]['context']['module']) &&
                        $metadata['components'][0]['rows'][1][0]['context']['module'] === 'Contacts' &&
                        !empty($metadata['components'][0]['rows'][1][0]['view']['display_columns'][0]) &&
                        $metadata['components'][0]['rows'][1][0]['view']['display_columns'][0] === 'full_name') {
                        $metadata['components'][0]['rows'][1][0]['view']['display_columns'][0] = 'name';
                        $updated = true;
                    }
                    break;
                case 'LBL_TASKS_LIST_DASHBOARD':
                    if (!empty($metadata['components'][0]['rows'][0][0]['context']['module']) &&
                        $metadata['components'][0]['rows'][0][0]['context']['module'] === 'Leads' &&
                        !empty($metadata['components'][0]['rows'][0][0]['view']['display_columns'][0]) &&
                        $metadata['components'][0]['rows'][0][0]['view']['display_columns'][0] === 'full_name') {
                        $metadata['components'][0]['rows'][0][0]['view']['display_columns'][0] = 'name';
                        $updated = true;
                    }
            }

            if ($updated) {
                $query = 'UPDATE dashboards SET metadata = ? WHERE id = ?';
                $this->db->getConnection()->executeUpdate(
                    $query,
                    [json_encode($metadata), $row['id']],
                    [\Doctrine\DBAL\ParameterType::STRING, \Doctrine\DBAL\ParameterType::STRING]
                );
                $this->log('"full_name" of ' . $row['name'] . ' has been replaced by "name"');
            }
        }
    }
}
