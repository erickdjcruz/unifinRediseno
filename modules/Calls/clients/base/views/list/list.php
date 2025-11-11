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
$viewdefs['Calls']['base']['view']['list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'label' => 'LBL_LIST_SUBJECT',
                    'enabled' => true,
                    'default' => true,
                    'link' => true,
                    'name' => 'name',
                    'related_fields' => ['repeat_type'],
                ],
                [
                    'name' => 'parent_name',
                    'label' => 'LBL_LIST_RELATED_TO',
                    'dynamic_module' => 'PARENT_TYPE',
                    'id' => 'PARENT_ID',
                    'link' => true,
                    'enabled' => true,
                    'default' => true,
                    'sortable' => false,
                    'ACLTag' => 'PARENT',
                    'related_fields' => [
                        'parent_id',
                        'parent_type',
                    ],
                ],
                [
                    'name' => 'date_start',
                    'label' => 'LBL_LIST_DATE',
                    'type' => 'datetimecombo-colorcoded',
                    'css_class' => 'overflow-visible',
                    'completed_status_value' => 'Held',
                    'enabled' => true,
                    'default' => true,
                    'readonly' => true,
                    'related_fields' => ['status'],
                ],
                [
                    'name' => 'date_end',
                    'link' => false,
                    'default' => false,
                    'enabled' => true,
                ],
                [
                    'enabled' => true,
                    'default' => true,
                    'name' => 'status',
                    'type' => 'event-status',
                    'css_class' => 'full-width',
                ],
                [
                    'enabled' => true,
                    'default' => true,
                    'name' => 'direction',
                ],
                [
                    'name' => 'assigned_user_name',
                    'target_record_key' => 'assigned_user_id',
                    'target_module' => 'Employees',
                    'label' => 'LBL_LIST_ASSIGNED_USER',
                    'enabled' => true,
                    'default' => true,
                    'sortable' => true,
                ],
                [
                    'name' => 'date_entered',
                    'enabled' => true,
                    'default' => true,
                    'readonly' => true,
                ],
            ],
        ],
    ],
];
