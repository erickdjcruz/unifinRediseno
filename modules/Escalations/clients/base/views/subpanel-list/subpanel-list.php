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
$viewdefs['Escalations']['base']['view']['subpanel-list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'label' => 'LBL_LIST_NUMBER',
                    'enabled' => true,
                    'default' => true,
                    'readonly' => true,
                    'name' => 'escalation_number',
                ],
                [
                    'label' => 'LBL_SUBJECT',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'name',
                    'link' => true,
                ],
                [
                    'label' => 'LBL_STATUS',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'status',
                ],
                [
                    'label' => 'LBL_LIST_RELATED_TO_TYPE',
                    'enabled' => true,
                    'default' => false,
                    'name' => 'parent_type',
                    'readonly' => true,
                ],
                [
                    'label' => 'LBL_LIST_RELATED_TO',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'parent_name',
                ],
                [
                    'label' => 'LBL_REASON',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'reason',
                ],
                [
                    'label' => 'LBL_DATE_MODIFIED',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'date_modified',
                ],
            ],
        ],
    ],
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
            [
                'type' => 'rowaction',
                'name' => 'edit_button',
                'label' => 'LBL_EDIT_BUTTON',
                'event' => 'list:editrow:fire',
                'acl_action' => 'edit',
            ],
        ],
    ],
];
