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

use Sugarcrm\Sugarcrm\AccessControl\AccessControlManager;

/**
 * Update the OmniChannel Dashboard.
 */
class SugarUpgradeUpdateOmniChannelDashboard extends UpgradeScript
{
    public $order = 7560;
    public $type = self::UPGRADE_DB;

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        if ($this->shouldUpdateOmniChannelDashboard()) {
            $this->updateOmniChannelDashboard();
        } else {
            $this->log('Not updating omnichannel dashboard');
        }

        $this->log('Adding create task button in Cases activity timeline dashlet for omnichannel dashboards ...');
        if (version_compare($this->from_version, '11.3.0', '<')) {
            $this->addCreateTaskToActivityTimelineDashlet();
        } else {
            $this->log('Not updating Cases activity timeline dashlet for omnichannel dashboard');
        }
    }

    private function addCreateTaskToActivityTimelineDashlet()
    {
        $activityDashletIndex = null;
        $this->log('Adding create task button for cases tab activity module in omnichannel dashboard');
        $defaultDashboardInstaller = new DefaultDashboardInstaller();
        $dashboardFile = 'modules/Dashboards/dashboards/omnichannel/omnichannel.php';
        $dashboardContents = $defaultDashboardInstaller->getFileContents($dashboardFile);
        $isUpdated = false;

        if (!empty($dashboardContents['id']) && !empty($dashboardContents['metadata'])) {
            $dashboardBean = BeanFactory::getBean('Dashboards', $dashboardContents['id']);
            if ($dashboardBean) {
                if (!empty($dashboardContents['metadata']['tabs'])) {
                    // Cases tab is the last tab on the dashboard
                    $casesTab = end($dashboardContents['metadata']['tabs']);

                    // safety check
                    if (!$casesTab['module'] || !$casesTab['module'] != 'Cases') {
                        // reset index pointer since we used end
                        reset($dashboardContents['metadata']['tabs']);

                        // expensive search to find Cases tab
                        foreach ($dashboardContents['metadata']['tabs'] as $index => $tab) {
                            if ($tab['module'] && $tab['module'] == 'Cases') {
                                $casesTab = $tab;
                                break;
                            }
                        }
                    }

                    $activityTimelineDashlet = [];

                    // find Activity Timeline Dashlet
                    foreach ($casesTab['dashlets'] as $index => $dashlet) {
                        if ($dashlet && $dashlet['view'] && $dashlet['view']['type'] == 'activity-timeline') {
                            $activityTimelineDashlet = $dashlet;
                            $activityDashletIndex = $index;
                        }
                    }

                    if (!$activityTimelineDashlet) {
                        $this->log('Unable to add create task button as activity-timeline dashlet was not found!');

                        return;
                    }

                    $createTaskBtn = [
                        'type' => 'dashletaction',
                        'action' => 'createRecord',
                        'params' => [
                            'link' => 'tasks',
                            'module' => 'Tasks',
                        ],
                        'label' => 'LBL_CREATE_TASK2',
                        'acl_action' => 'create',
                        'acl_module' => 'Tasks',
                    ];

                    $customToolbarButtons = $activityTimelineDashlet['view']['custom_toolbar']['buttons'];
                    array_push($customToolbarButtons, $createTaskBtn);

                    // give it back to $dashboardContents
                    if ($dashboardContents['metadata']['tabs'][$casesTab]['dashlets'][$activityDashletIndex]['view']
                        && $dashboardContents['metadata']['tabs'][$casesTab]['dashlets'][$activityDashletIndex]
                        ['view']['custom_toolbar']
                        && $dashboardContents['metadata']['tabs'][$casesTab]['dashlets'][$activityDashletIndex]
                        ['view']['custom_toolbar']['buttons']) {
                        $dashboardContents['metadata']['tabs'][$casesTab]['dashlets'][$activityDashletIndex]
                        ['view']['custom_toolbar']['buttons'] = $customToolbarButtons;
                    }
                }

                $dashboardBean->metadata = json_encode($dashboardContents['metadata']);
                $dashboardBean->save();
                $isUpdated = true;
            }
        }

        if ($isUpdated) {
            $this->log('Create task button added successfully to the omnichannel dashboard');
        } else {
            $this->log('Omnichannel dashboard not updated');
        }
    }


    /**
     * Determine if we should update omnichannel dashboard
     *
     * @return bool true if we should update the omnichannel dashboard; false otherwise
     */
    private function shouldUpdateOmniChannelDashboard(): bool
    {
        $isFlavorConversion = !$this->fromFlavor('ent') && $this->toFlavor('ent');
        $isRightFlavorNVersion = $this->toFlavor('ent') &&
            version_compare($this->from_version, '11.1.0', '<');
        return $isFlavorConversion || $isRightFlavorNVersion;
    }

    /**
     * Update the omnichannel dashboard
     */
    private function updateOmniChannelDashboard()
    {
        $this->log('Updating omnichannel dashboard');

        $defaultDashboardInstaller = new DefaultDashboardInstaller();
        $dashboardFile = 'modules/Dashboards/dashboards/omnichannel/omnichannel.php';
        $dashboardContents = $defaultDashboardInstaller->getFileContents($dashboardFile);
        $isUpdated = false;
        if (!empty($dashboardContents['id']) && !empty($dashboardContents['metadata'])) {
            $dashboardBean = BeanFactory::getBean('Dashboards', $dashboardContents['id']);
            if ($dashboardBean) {
                $oldTabMeta = $this->getOldTabMeta($dashboardBean, ['LBL_CONTACT', 'LBL_CASE']);
                if (!empty($dashboardContents['metadata']['tabs'])) {
                    foreach ($dashboardContents['metadata']['tabs'] as $idx => $tab) {
                        if ($tab['name'] === 'LBL_CONTACT' &&
                            array_key_exists($tab['name'], $oldTabMeta)) {
                            $defaultMeta = $this->getOldDefaultContactMeta();
                            // Contacts tab defs is configured by user prior to 11.1.0, keep it
                            if ($oldTabMeta[$tab['name']] !== $defaultMeta) {
                                $dashboardContents['metadata']['tabs'][$idx] = $oldTabMeta[$tab['name']];
                            }
                        } elseif ($tab['name'] === 'LBL_CASE' &&
                            // keep old Cases tab defs since no new defs defined on 11.1.0
                            array_key_exists($tab['name'], $oldTabMeta)) {
                            $dashboardContents['metadata']['tabs'][$idx] = $oldTabMeta[$tab['name']];
                        }
                    }
                }
                $dashboardBean->metadata = json_encode($dashboardContents['metadata']);
                $dashboardBean->save();
                $isUpdated = true;
            }
        }
        if (!$isUpdated) {
            $this->log('omnichannel dashboard is not updated');
        }
    }

    /**
     * Gets the tabs from the old omnichannel dashboard
     *
     * @param SugarBean $dashboardBean a DashBoards bean.
     * @param array $modules module array.
     * @return array
     */
    private function getOldTabMeta(SugarBean $dashboardBean, array $modules): array
    {
        $oldTabMeta = [];
        if (!empty($dashboardBean->metadata)) {
            $oldMetadata = json_decode($dashboardBean->metadata, true);
            if (!empty($oldMetadata['tabs'])) {
                foreach ($oldMetadata['tabs'] as $oldTab) {
                    if (in_array($oldTab['name'], $modules)) {
                        $oldTabMeta[$oldTab['name']] = $oldTab;
                    }
                }
            }
        }
        return $oldTabMeta;
    }

    /**
     * Gets the old default Contacts tab metadata
     *
     * @return array
     */
    private function getOldDefaultContactMeta(): array
    {
        return [
            'icon' => [
                'module' => 'Contacts',
            ],
            'name' => 'LBL_CONTACT',
            'dashlets' => [
                [
                    'view' => [
                        'type' => 'dashablerecord',
                        'module' => 'Contacts',
                        'tabs' => [
                            [
                                'active' => true,
                                'label' => 'LBL_MODULE_NAME_SINGULAR',
                                'link' => '',
                                'module' => 'Contacts',
                            ],
                            [
                                'active' => false,
                                'label' => 'LBL_MODULE_NAME_SINGULAR',
                                'link' => 'accounts',
                                'module' => 'Accounts',
                            ],
                        ],
                        'tab_list' => [
                            'Contacts',
                            'accounts',
                        ],
                    ],
                    'context' => [
                        'module' => 'Contacts',
                    ],
                    'width' => 6,
                    'height' => 8,
                    'x' => 0,
                    'y' => 0,
                    'autoPosition' => false,
                ],
                [
                    'view' => [
                        'type' => 'dashlet-searchable-kb-list',
                        'label' => 'LBL_DASHLET_KB_SEARCH_NAME',
                        'data_provider' => 'Categories',
                        'config_provider' => 'KBContents',
                        'root_name' => 'category_root',
                        'extra_provider' => [
                            'module' => 'KBContents',
                            'field' => 'category_id',
                        ],
                    ],
                    'context' => [
                        'module' => 'KBContents',
                    ],
                    'width' => 6,
                    'height' => 8,
                    'x' => 6,
                    'y' => 0,
                    'autoPosition' => false,
                ],
                [
                    'view' => [
                        'type' => 'dashlet-console-list',
                        'module' => 'Cases',
                    ],
                    'context' => [
                        'module' => 'Cases',
                    ],
                    'width' => 6,
                    'height' => 8,
                    'x' => 0,
                    'y' => 8,
                    'autoPosition' => false,
                ],
                [
                    'view' => [
                        'type' => 'activity-timeline',
                        'label' => 'TPL_ACTIVITY_TIMELINE_DASHLET',
                        'module' => 'Contacts',
                        'custom_toolbar' => [
                            'buttons' => [
                                [
                                    'type' => 'actiondropdown',
                                    'no_default_action' => true,
                                    'icon' => 'sicon-plus',
                                    'buttons' => [
                                        [
                                            'type' => 'dashletaction',
                                            'action' => 'composeEmail',
                                            'params' => [
                                                'link' => 'emails',
                                                'module' => 'Emails',
                                            ],
                                            'label' => 'LBL_COMPOSE_EMAIL_BUTTON_LABEL',
                                            'icon' => 'sicon-plus',
                                            'acl_action' => 'create',
                                            'acl_module' => 'Emails',
                                        ],
                                        [
                                            'type' => 'dashletaction',
                                            'action' => 'createRecord',
                                            'params' => [
                                                'link' => 'calls',
                                                'module' => 'Calls',
                                            ],
                                            'label' => 'LBL_SCHEDULE_CALL',
                                            'icon' => 'sicon-phone',
                                            'acl_action' => 'create',
                                            'acl_module' => 'Calls',
                                        ],
                                        [
                                            'type' => 'dashletaction',
                                            'action' => 'createRecord',
                                            'params' => [
                                                'link' => 'meetings',
                                                'module' => 'Meetings',
                                            ],
                                            'label' => 'LBL_SCHEDULE_MEETING',
                                            'icon' => 'sicon-calendar',
                                            'acl_action' => 'create',
                                            'acl_module' => 'Meetings',
                                        ],
                                        [
                                            'type' => 'dashletaction',
                                            'action' => 'createRecord',
                                            'params' => [
                                                'link' => 'notes',
                                                'module' => 'Notes',
                                            ],
                                            'label' => 'LBL_CREATE_NOTE_OR_ATTACHMENT',
                                            'icon' => 'sicon-plus',
                                            'acl_action' => 'create',
                                            'acl_module' => 'Notes',
                                        ],
                                    ],
                                ],
                                [
                                    'type' => 'dashletaction',
                                    'css_class' => 'btn btn-invisible dashlet-toggle minify',
                                    'icon' => 'sicon-chevron-up',
                                    'action' => 'toggleMinify',
                                    'tooltip' => 'LBL_DASHLET_TOGGLE',
                                    'disallowed_layouts' => [
                                        [
                                            'name' => 'omnichannel-dashboard',
                                        ],
                                    ],
                                ],
                                [
                                    'dropdown_buttons' => [
                                        [
                                            'type' => 'dashletaction',
                                            'action' => 'editClicked',
                                            'label' => 'LBL_DASHLET_CONFIG_EDIT_LABEL',
                                        ],
                                        [
                                            'type' => 'dashletaction',
                                            'action' => 'reloadData',
                                            'label' => 'LBL_DASHLET_REFRESH_LABEL',
                                        ],
                                        [
                                            'type' => 'dashletaction',
                                            'action' => 'removeClicked',
                                            'label' => 'LBL_DASHLET_REMOVE_LABEL',
                                            'name' => 'remove_button',
                                            'disallowed_layouts' => [
                                                [
                                                    'name' => 'omnichannel-dashboard',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'context' => [
                        'module' => 'Contacts',
                    ],
                    'width' => 6,
                    'height' => 8,
                    'x' => 6,
                    'y' => 8,
                    'autoPosition' => false,
                ],
            ],
        ];
    }
}
