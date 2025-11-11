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
 * Update icon metadata for some dashboards
 */
class SugarUpgradeUpdateIconDashboardMetadata extends UpgradeScript
{
    public $order = 9501;
    public $type = self::UPGRADE_DB;

    /**
     * @throws SugarQueryException
     */
    public function run()
    {
        $this->log('Updating icon metadata for some dashboards ...');
        if (version_compare($this->from_version, '11.2.0', '>=')) {
            return;
        }

        $consoleIDs = [
            '32bc5cd0-b1a0-11ea-ad16-f45c898a3ce7', // omnichannel dashboard
            'c290ef46-7606-11e9-9129-f218983a1c3e', // case multi line dashboard
            'd8f610a0-e950-11e9-81b4-2a2ae2dbcce4', // account multi line dashboard
            '069a1142-61bf-473f-8014-faca9aaf43cf', // opportunity multi line dashboard
        ];

        $bean = BeanFactory::newBean('Dashboards');
        $query = new SugarQuery();
        $query->select(['id', 'name', 'metadata']);
        $query->from($bean);
        $query->where()->in('id', $consoleIDs);
        $rows = $query->execute();

        foreach ($rows as $row) {
            $metadata = json_decode($row['metadata'], true);
            $updated = false;

            switch ($row['id']) {
                // omnichannel dashboard
                case '32bc5cd0-b1a0-11ea-ad16-f45c898a3ce7':
                    $updated = $this->processOmnichannel($metadata);
                    break;
                    // case multi line dashboard
                case 'c290ef46-7606-11e9-9129-f218983a1c3e':
                    $updated = $this->processMultiline($metadata, 'Cases');
                    break;
                    // account multi line dashboard
                case 'd8f610a0-e950-11e9-81b4-2a2ae2dbcce4':
                    $updated = $this->processMultiline($metadata, 'Accounts');
                    break;
                    // opportunity multi line dashboard
                case '069a1142-61bf-473f-8014-faca9aaf43cf':
                    $updated = $this->processMultiline($metadata, 'Opportunities');
                    break;
            }

            if ($updated) {
                $qb = $this->db->getConnection()->createQueryBuilder();
                $qb->update('dashboards')
                    ->set('metadata', $qb->createPositionalParameter(json_encode($metadata)))
                    ->where($qb->expr()->eq('id', $qb->createPositionalParameter($row['id'])));
                $qb->execute();
                $this->log('Icon Metadata is updated for dashboard name = ' . $row['name']);
            }
        }
        $this->log('Console icon metadata update complete!');
    }

    /**
     * Update the omnichannel dashboard
     * @param $metadata
     * @return bool
     */
    private function processOmnichannel(&$metadata): bool
    {
        if (isset($metadata['tabs'])) {
            foreach ($metadata['tabs'] as &$tab) {
                if (isset($tab['icon']['module']) && $tab['icon']['module'] == 'Cases') {
                    $tab['module'] = 'Cases';
                    foreach ($tab['dashlets'] as &$dashlet) {
                        if (isset($dashlet['view']['type'])
                            && isset($dashlet['view']['module'])
                            && $dashlet['view']['type'] == 'activity-timeline'
                            && $dashlet['view']['module'] == 'Cases') {
                            return $this->updateIcon($dashlet['view']['custom_toolbar']['buttons']);
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * Update the multi line dashboard
     * @param $metadata
     * @param $module
     * @return bool
     */
    private function processMultiline(&$metadata, $module): bool
    {
        if (isset($metadata['components'])) {
            foreach ($metadata['components'] as &$component) {
                foreach ($component['rows'] as &$row) {
                    foreach ($row as &$view) {
                        if (isset($view['view']) && isset($view['view']['type']) && isset($view['view']['module'])) {
                            if ($view['view']['type'] == 'activity-timeline' && $view['view']['module'] == $module) {
                                if (isset($view['view']['custom_toolbar']['buttons'])) {
                                    return $this->updateIcon($view['view']['custom_toolbar']['buttons']);
                                }
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * Update the icon property for dashboards
     * @param $buttons
     * @return bool
     */
    private function updateIcon(&$buttons): bool
    {
        $updated = false;
        if (!is_array($buttons)) {
            return $updated;
        }
        foreach ($buttons as &$button) {
            if ($button['type'] == 'actiondropdown' && is_array($button['buttons'])) {
                if (isset($button['icon']) && $button['icon'] !== 'sicon-plus') {
                    $button['icon'] = 'sicon-plus';
                    $updated = true;
                }
            } elseif ($button['type'] == 'dashletaction' && $button['action'] == 'toggleMinify') {
                if (isset($button['icon']) && $button['icon'] !== 'sicon-chevron-up') {
                    $button['icon'] = 'sicon-chevron-up';
                    $updated = true;
                }
            }
        }
        return $updated;
    }
}
