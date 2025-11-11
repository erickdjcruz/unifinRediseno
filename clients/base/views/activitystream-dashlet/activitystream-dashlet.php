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

$viewdefs['base']['view']['activitystream-dashlet'] = [
    'dashlets' => [
        [
            'label' => 'LBL_ACTIVITY_STREAM_DASHLET_NAME',
            'description' => 'LBL_ACTIVITY_STREAM_DASHLET_DESCRIPTION',
            'config' => [
                'module' => 'Activities',
                'limit' => 5,
            ],
            'preview' => [
                'module' => 'Activities',
                'limit' => 5,
            ],
            'filter' => [
                'view' => [
                    'record',
                    'records',
                ],
            ],
        ],
    ],
    'custom_toolbar' => [
        'buttons' => [
            [
                'type' => 'dashletaction',
                'css_class' => 'btn btn-invisible addComment',
                'icon' => 'sicon-plus',
                'action' => 'addComment',
                'tooltip' => 'New comment',
            ],
            [
                'type' => 'dashletaction',
                'css_class' => 'dashlet-toggle btn btn-invisible minify',
                'icon' => 'sicon-chevron-up',
                'action' => 'toggleMinify',
                'tooltip' => 'LBL_DASHLET_MINIMIZE',
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
                        'action' => 'refreshClicked',
                        'label' => 'LBL_DASHLET_REFRESH_LABEL',
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'toggleClicked',
                        'label' => 'LBL_DASHLET_MINIMIZE',
                        'event' => 'minimize',
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'removeClicked',
                        'label' => 'LBL_DASHLET_REMOVE_LABEL',
                    ],
                ],
            ],
        ],
    ],
    'config' => [
        'fields' => [
            [
                'name' => 'limit',
                'label' => 'LBL_DASHLET_CONFIGURE_DISPLAY_ROWS',
                'type' => 'enum',
                'options' => 'dashlet_limit_options',
            ],
            [
                'name' => 'auto_refresh',
                'label' => 'LBL_REPORT_AUTO_REFRESH',
                'type' => 'enum',
                'options' => 'sugar7_dashlet_auto_refresh_options',
            ],
        ],
    ],
];
