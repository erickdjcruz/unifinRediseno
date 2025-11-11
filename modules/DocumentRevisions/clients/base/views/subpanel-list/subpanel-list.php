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
$viewdefs['DocumentRevisions']['base']['view']['subpanel-list'] = [
    'rowactions' => [
        'actions' => [
            [
                'type' => 'rowaction',
                'css_class' => 'btn',
                'tooltip' => 'LBL_PREVIEW',
                'event' => 'list:preview:fire',
                'icon' => 'sicon-preview',
                'acl_action' => 'view',
            ],
        ],
    ],
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'default' => true,
                    'label' => 'LBL_FILENAME',
                    'enabled' => true,
                    'name' => 'filename',
                    'link' => true,
                    'type' => 'relate',
                ],
                [
                    'default' => true,
                    'label' => 'LBL_REVISION',
                    'enabled' => true,
                    'name' => 'revision',
                ],
                [
                    'default' => true,
                    'label' => 'LBL_CREATED',
                    'enabled' => true,
                    'name' => 'created_by_name',
                ],
                [
                    'default' => true,
                    'label' => 'LBL_CHANGE_LOG',
                    'enabled' => true,
                    'name' => 'change_log',
                ],
            ],
        ],
    ],
];
