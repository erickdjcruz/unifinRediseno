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
$viewdefs['Reports']['base']['view']['report-header'] = [
    'panels' => [
        [
            'name' => 'header',
            'fields' => [
                [
                    'name' => 'name',
                    'placeholder' => 'LBL_TITLE',
                ],
                [
                    'name' => 'my_favorite',
                    'label' => 'LBL_FAVORITE',
                    'type' => 'favorite',
                    'dismiss_label' => true,
                ],
            ],
            'buttons' => [
                [
                    'type' => 'visibility-widget',
                ],
                [
                    'type' => 'orientation-widget',
                ],
                [
                    'type' => 'actiondropdown',
                    'name' => 'main_dropdown',
                    'primary' => true,
                    'showOn' => 'view',
                    'buttons' => [
                        [
                            'type' => 'rowaction',
                            'event' => 'button:refresh:click',
                            'name' => 'refresh_button',
                            'label' => 'LBL_REFRESH_BUTTON_LABEL',
                        ],
                        [
                            'type' => 'rowaction',
                            'event' => 'button:edit:click',
                            'name' => 'edit',
                            'label' => 'LBL_EDIT_BUTTON_LABEL',
                            'acl_action' => 'edit',
                        ],
                        [
                            'type' => 'shareaction',
                            'name' => 'share',
                            'label' => 'LBL_SHARE_BUTTON_LABEL',
                        ],
                        [
                            'type' => 'rowaction',
                            'event' => 'button:copy:click',
                            'name' => 'copy',
                            'label' => 'LBL_DUPLICATE_BUTTON_LABEL',
                        ],
                        [
                            'type' => 'rowaction',
                            'event' => 'button:schedule:click',
                            'name' => 'schedule',
                            'label' => 'LBL_REPORT_SCHEDULES_BUTTON_LABEL',
                        ],
                        [
                            'type' => 'rowaction',
                            'event' => 'button:export:click',
                            'name' => 'export',
                            'label' => 'LBL_EXPORT',
                            'acl_action' => 'export',
                        ],
                        [
                            'type' => 'rowaction',
                            'event' => 'button:delete:click',
                            'name' => 'delete',
                            'label' => 'LBL_DELETE_BUTTON_LABEL',
                            'acl_action' => 'delete',
                        ],
                        [
                            'type' => 'rowaction',
                            'event' => 'button:details:click',
                            'name' => 'details',
                            'label' => 'LBL_REPORT_DETAILS_BUTTON_LABEL',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
