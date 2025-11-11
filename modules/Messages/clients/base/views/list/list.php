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

$viewdefs['Messages']['base']['view']['list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'label' => 'LBL_MESSAGE_SUBJECT',
                    'link' => true,
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'parent_name',
                    'label' => 'LBL_RELATED_TO',
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
                    'label' => 'LBL_START_DATE',
                    'link' => false,
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'date_end',
                    'label' => 'LBL_END_DATE',
                    'link' => false,
                    'default' => false,
                    'enabled' => true,
                ],
                [
                    'name' => 'status',
                    'label' => 'LBL_STATUS',
                    'link' => false,
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'direction',
                    'label' => 'LBL_DIRECTION',
                    'link' => false,
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'created_by_name',
                    'type' => 'relate',
                    'label' => 'LBL_CREATED_BY',
                    'enabled' => true,
                    'default' => true,
                    'related_fields' => ['created_by'],
                ],
                [
                    'name' => 'date_entered',
                    'enabled' => true,
                    'default' => true,
                ],
            ],
        ],
    ],
];
