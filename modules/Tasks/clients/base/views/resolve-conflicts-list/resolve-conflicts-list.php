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
$viewdefs['Tasks']['base']['view']['resolve-conflicts-list'] = [
    'panels' => [
        [
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'link' => true,
                    'label' => 'LBL_LIST_SUBJECT',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'contact_name',
                    'label' => 'LBL_LIST_CONTACT',
                    'link' => true,
                    'id' => 'CONTACT_ID',
                    'module' => 'Contacts',
                    'enabled' => true,
                    'default' => true,
                    'ACLTag' => 'CONTACT',
                    'related_fields' => ['contact_id'],
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
                    'related_fields' => ['parent_id', 'parent_type'],
                ],
                [
                    'name' => 'date_due',
                    'label' => 'LBL_LIST_DUE_DATE',
                    'link' => false,
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'team_name',
                    'label' => 'LBL_LIST_TEAM',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_LIST_ASSIGNED_TO_NAME',
                    'id' => 'ASSIGNED_USER_ID',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'date_start',
                    'label' => 'LBL_LIST_START_DATE',
                    'link' => false,
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'status',
                    'label' => 'LBL_LIST_STATUS',
                    'link' => false,
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'date_entered',
                    'label' => 'LBL_DATE_ENTERED',
                    'enabled' => true,
                    'default' => false,
                    'readonly' => true,
                ],
            ],
        ],
    ],
];
