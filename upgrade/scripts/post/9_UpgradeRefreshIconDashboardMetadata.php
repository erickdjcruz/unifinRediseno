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
class SugarUpgradeUpgradeRefreshIconDashboardMetadata extends UpgradeScript
{
    public $order = 9501;

    public $type = self::UPGRADE_DB;

    public array $btnMeta = [
        'type' => 'dashletaction',
        'css_class' => 'btn btn-invisible',
        'icon' => 'sicon-refresh',
        'action' => 'reloadData',
        'tooltip' => 'LBL_DASHLET_REFRESH_LABEL',
    ];

    /**
     * Execute upgrade tasks
     * @see UpgradeScript::run()
     */
    public function run(): void
    {
        $this->log('Updating icon metadata for some dashboards ...');
        if (version_compare($this->from_version, '13.2.0', '<=') &&
            version_compare($this->to_version, '13.3.0', '>=')) {
            return;
        }

        $consoleIDs = [
            '32bc5cd0-b1a0-11ea-ad16-f45c898a3ce7', // omnichannel dashboard
            'c290ef46-7606-11e9-9129-f218983a1c3e', // case multi line dashboard
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
                    $updated = $this->processMultiline($metadata);
                    break;
            }

            if ($updated) {
                $qb = $this->db->getConnection()->createQueryBuilder();
                $qb->update('dashboards')
                    ->set('metadata', $qb->createPositionalParameter(json_encode($metadata)))
                    ->where($qb->expr()->eq('id', $qb->createPositionalParameter($row['id'])));
                $qb->execute();
                $name = translate($row['name'], 'Cases');
                $this->log("Refresh icon metadata is updated for dashboard name = \"$name\"");
            }
        }
    }

    private function processMultiline(&$metadata): bool
    {
        return $this->upgradeDashletMeta($metadata['dashlets']);
    }

    private function processOmnichannel(&$metadata): bool
    {
        $updated = false;
        foreach ($metadata['tabs'] as &$tab) {
            if ($this->upgradeDashletMeta($tab['dashlets'])) {
                $updated = true;
            }
        }
        return $updated;
    }

    private function upgradeDashletMeta(&$dashletsMeta): bool
    {
        $metaUpdated = false;

        if (!$dashletsMeta) {
            return $metaUpdated;
        }

        foreach ($dashletsMeta as &$dashletMeta) {
            if (isset($dashletMeta['view']) && ($dashletMeta['view']['type'] ?? '') === 'activity-timeline') {
                $buttons = &$dashletMeta['view']['custom_toolbar']['buttons'];
                $updated = false;

                if (!$buttons) {
                    continue;
                }

                foreach ($buttons as &$buttonMeta) {
                    if (isset($buttonMeta['dropdown_buttons'])) {
                        $dropdownButtons = &$buttonMeta['dropdown_buttons'];
                        foreach ($dropdownButtons as $k => &$dropdownActionMeta) {
                            if (($dropdownActionMeta['action'] ?? '') === 'reloadData') {
                                array_splice($dropdownButtons, $k, 1);
                                $updated = true;
                            }
                        }
                    }
                }

                if ($updated) {
                    array_splice($buttons, 1, 0, [$this->btnMeta]);
                    $metaUpdated = true;
                }
            }
        }

        return $metaUpdated;
    }
}
