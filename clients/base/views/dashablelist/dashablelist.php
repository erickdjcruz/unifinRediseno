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

$viewdefs['base']['view']['dashablelist'] = [
    'template' => 'list',
    'dashlets' => [
        [
            'label' => 'LBL_DASHLET_LISTVIEW_NAME',
            'description' => 'LBL_DASHLET_LISTVIEW_DESCRIPTION',
            'config' => [],
            'preview' => [
                'module' => 'Accounts',
                'label' => 'LBL_MODULE_NAME',
                'display_columns' => [
                    'name',
                    'phone_office',
                    'billing_address_country',
                ],
            ],
        ],
    ],
    'panels' => [
        [
            'name' => 'dashlet_settings',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'module',
                    'label' => 'LBL_MODULE',
                    'type' => 'enum',
                    'span' => 12,
                    'sort_alpha' => true,
                ],
                [
                    'name' => 'display_columns',
                    'label' => 'LBL_COLUMNS',
                    'type' => 'enum',
                    'isMultiSelect' => true,
                    'ordered' => true,
                    'span' => 12,
                    'hasBlank' => true,
                    'options' => ['' => ''],
                ],
                [
                    'name' => 'freeze_first_column',
                    'label' => 'LBL_DASHLET_FREEZE_FIRST_COLUMN',
                    'type' => 'bool',
                    'span' => 12,
                    'default' => true,
                    'showOnConfig' => 'allowFreezeFirstColumn',
                ],
                [
                    'name' => 'limit',
                    'label' => 'LBL_DASHLET_CONFIGURE_DISPLAY_ROWS',
                    'type' => 'enum',
                    'options' => 'dashlet_limit_options',
                ],
                [
                    'name' => 'auto_refresh',
                    'label' => 'Auto Refresh',
                    'type' => 'enum',
                    'options' => 'sugar7_dashlet_auto_refresh_options',
                ],
                [
                    'name' => 'intelligent',
                    'label' => 'LBL_DASHLET_CONFIGURE_INTELLIGENT',
                    'type' => 'bool',
                ],
                [
                    'name' => 'linked_fields',
                    'label' => 'LBL_DASHLET_CONFIGURE_LINKED',
                    'type' => 'enum',
                    'required' => true,
                ],
            ],
        ],
    ],
];
