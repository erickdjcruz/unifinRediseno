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
 * Update metadata for some dashboards
 */
class SugarUpgradeUpdateDashboardMetadata extends UpgradeScript
{
    public $order = 9500;
    public $type = self::UPGRADE_DB;

    /**
     * @throws SugarQueryException
     */
    public function run()
    {
        $this->log('Updating metadata for some dashboards ...');
        if (version_compare($this->from_version, '11.1.0', '>=')) {
            return;
        }

        $consoleIDs = [
            'c108bb4a-775a-11e9-b570-f218983a1c3e', // Service Console
            'da438c86-df5e-11e9-9801-3c15c2c53980', // Renewals Console
            '0ca2d773-0bb3-4bf3-ae43-68569968af57', // Portal Home
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
                // Service console
                case 'c108bb4a-775a-11e9-b570-f218983a1c3e':
                    $updated = $this->updateServiceConsole($metadata);
                    break;
                    // Renewals console
                case 'da438c86-df5e-11e9-9801-3c15c2c53980':
                    $updated = $this->updateRenewalsConsole($metadata);
                    break;
                    // Portal Home
                case '0ca2d773-0bb3-4bf3-ae43-68569968af57':
                    $updated = $this->updatePortalHome($metadata);
                    break;
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
                $query = 'UPDATE dashboards SET metadata = ? WHERE id = ?';
                $this->db->getConnection()->executeUpdate(
                    $query,
                    [json_encode($metadata), $row['id']],
                    [\Doctrine\DBAL\ParameterType::STRING, \Doctrine\DBAL\ParameterType::STRING]
                );
                $this->log('Metadata is updated for dashboard name = ' . $row['name']);
            }
        }
        $this->log('Console metadata update complete!');
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
                    $tab['module'] = 'Cases'; // per SS-1433, adding the module attribute
                    foreach ($tab['dashlets'] as &$dashlet) {
                        if (isset($dashlet['view']['type'])
                            && isset($dashlet['view']['module'])
                            && $dashlet['view']['type'] == 'activity-timeline'
                            && $dashlet['view']['module'] == 'Cases') {
                            $this->updateActionLabel($dashlet['view']['custom_toolbar']['buttons']);
                            break;
                        }
                    }
                    return true;
                } elseif (isset($tab['name']) && $tab['name'] == 'LBL_SEARCH') {
                    if (isset($tab['icon']['image'])) {
                        // quotes need to be escaped
                        $tab['icon']['image'] = addslashes($tab['icon']['image']);
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
                                    return $this->updateActionLabel($view['view']['custom_toolbar']['buttons']);
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
     * Update the action label and delete the icon property for dashboards
     * @param $buttons
     * @return bool
     */
    private function updateActionLabel(&$buttons): bool
    {
        $updated = false;
        if (!is_array($buttons)) {
            return $updated;
        }
        $labels = [
            'LBL_COMPOSE_EMAIL_BUTTON_LABEL',
            'LBL_SCHEDULE_CALL',
            'LBL_SCHEDULE_MEETING',
            'LBL_CREATE_NOTE_OR_ATTACHMENT',
            'LBL_CREATE_MESSAGE',
        ];
        foreach ($buttons as &$button) {
            if ($button['type'] == 'actiondropdown' && is_array($button['buttons'])) {
                foreach ($button['buttons'] as &$btn) {
                    if (in_array($btn['label'], $labels)) {
                        // update label
                        $btn['label'] .= '2';
                        // remove icon
                        unset($btn['icon']);
                        $updated = true;
                    }
                }
                break;
            }
        }
        return $updated;
    }

    /**
     * Update the portal home dashboard metadata
     * @param $metadata
     * @return bool
     */
    private function updatePortalHome(&$metadata): bool
    {
        $updated = false;
        // to remove the search dashlet per CS-1594
        if (isset($metadata['components'][0]['rows'][0][0]['view'])
            && $metadata['components'][0]['rows'][0][0]['view']['type'] == 'contentsearchdashlet') {
            array_shift($metadata['components'][0]['rows']);
            $updated = true;
        }

        // to add the buttons per CS-1661
        $buttons = $this->getNewButtonsMeta();
        if (isset($metadata['components'][0]['rows'][0])) {
            foreach ($metadata['components'][0]['rows'][0] as &$component) {
                $component['view']['custom_toolbar']['buttons'] = $buttons;
                $updated = true;
            }
        }
        return $updated;
    }

    /**
     * Update the service console dashboard metadata
     * @param $metadata
     * @return bool
     */
    private function updateServiceConsole(&$metadata): bool
    {
        $filterMeta = $this->getConsoleFilterMeta('Cases');

        // Add filterpanel to Cases tab
        if ($metadata['tabs'] && $metadata['tabs'][1] && $metadata['tabs'][1]['components']) {
            array_unshift($metadata['tabs'][1]['components'], $filterMeta);
            return true;
        }
        return false;
    }

    /**
     * Update the renewals console dashboard metadata
     * @param $metadata
     * @return bool
     */
    private function updateRenewalsConsole(&$metadata): bool
    {
        $updated = false;
        $filterMetaAcc = $this->getConsoleFilterMeta('Accounts');
        $filterMetaOpps = $this->getConsoleFilterMeta('Opportunities');

        if ($metadata['tabs']) {
            // Add filterpanel to Accounts tab
            if ($metadata['tabs'][1] && $metadata['tabs'][1]['components']) {
                array_unshift($metadata['tabs'][1]['components'], $filterMetaAcc);
                $updated = true;
            }

            // Add filterpanel to Opportunities tab
            if ($metadata['tabs'][2] && $metadata['tabs'][2]['components']) {
                array_unshift($metadata['tabs'][2]['components'], $filterMetaOpps);
                $updated = true;
            }
        }
        return $updated;
    }

    /**
     * Get the new button metadata
     * @return string[][][][]
     */
    private function getNewButtonsMeta(): array
    {
        return [
            [
                'dropdown_buttons' => [
                    [
                        'type' => 'dashletaction',
                        'action' => 'editClicked',
                        'label' => 'LBL_DASHLET_CONFIG_EDIT_LABEL',
                        'name' => 'edit_button',
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'removeClicked',
                        'label' => 'LBL_DASHLET_REMOVE_LABEL',
                        'name' => 'remove_button',
                    ],
                ],
            ],
        ];
    }

    /**
     * Get the console Filter metadata
     * @param string $module
     * @return array
     */
    private function getConsoleFilterMeta(string $module): array
    {
        if ($module) {
            return [
                'context' => [
                    'module' => $module,
                ],
                'layout' => 'multi-line-filterpanel',
            ];
        }
    }
}
