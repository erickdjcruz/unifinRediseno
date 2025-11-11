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
/*********************************************************************************
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
$viewdefs['Notes']['mobile']['view']['list'] = [
    'panels' => [
        [
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => [
                [
                    'name' => 'name',
                    'label' => 'LBL_LIST_SUBJECT',
                    'default' => true,
                    'enabled' => true,
                    'link' => true,
                ],
                [
                    'name' => 'date_modified',
                    'label' => 'LBL_LIST_DATE_MODIFIED',
                    'enabled' => true,
                    'default' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'date_entered',
                    'label' => 'LBL_LIST_DATE_ENTERED',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_LIST_ASSIGNED_TO_NAME',
                    'target_module' => 'Employees',
                    'target_record_key' => 'assigned_user_id',
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
                    'ACLTag' => 'PARENT',
                    'related_fields' => [
                        'parent_id',
                        'parent_type',
                    ],
                ],
                [
                    'name' => 'created_by_name',
                    'type' => 'relate',
                    'label' => 'LBL_CREATED_BY',
                    'enabled' => true,
                    'default' => true,
                    'related_fields' => ['created_by'],
                ],
            ],
        ],
    ],
];
