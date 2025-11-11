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
$listViewDefs ['Meetings'] =
    [
        'SET_COMPLETE' => [
            'width' => '1',
            'label' => 'LBL_LIST_CLOSE',
            'link' => true,
            'sortable' => false,
            'default' => true,
            'related_fields' => [
                0 => 'status',
            ],
        ],
        'JOIN_MEETING' => [
            'width' => '1',
            'label' => 'LBL_LIST_JOIN_MEETING',
            'link' => true,
            'sortable' => false,
            'default' => true,
            'related_fields' => ['join_url', 'host_url'],
            'noHeader' => true,
        ],
        'NAME' => [
            'width' => '40',
            'label' => 'LBL_LIST_SUBJECT',
            'link' => true,
            'default' => true,
        ],
        'CONTACT_NAME' => [
            'width' => '20',
            'label' => 'LBL_LIST_CONTACT',
            'link' => true,
            'id' => 'CONTACT_ID',
            'module' => 'Contacts',
            'default' => true,
            'ACLTag' => 'CONTACT',
        ],
        'PARENT_NAME' => [
            'width' => '20',
            'label' => 'LBL_LIST_RELATED_TO',
            'dynamic_module' => 'PARENT_TYPE',
            'id' => 'PARENT_ID',
            'link' => true,
            'default' => true,
            'sortable' => false,
            'ACLTag' => 'PARENT',
            'related_fields' => [
                0 => 'parent_id',
                1 => 'parent_type',
            ],
        ],
        'DATE_START' => [
            'width' => '15',
            'label' => 'LBL_LIST_DATE',
            'link' => false,
            'default' => true,
            'related_fields' => [
                0 => 'time_start',
            ],
        ],
        'TEAM_NAME' => [
            'width' => '2',
            'label' => 'LBL_LIST_TEAM',
            'default' => false,
        ],
        'ASSIGNED_USER_NAME' => [
            'width' => '2',
            'label' => 'LBL_LIST_ASSIGNED_TO_NAME',
            'module' => 'Employees',
            'id' => 'ASSIGNED_USER_ID',
            'default' => true,
        ],
        'DIRECTION' => [
            'type' => 'enum',
            'label' => 'LBL_LIST_DIRECTION',
            'width' => '10',
            'default' => false,
        ],
        'STATUS' => [
            'width' => '10',
            'label' => 'LBL_LIST_STATUS',
            'link' => false,
            'default' => false,
        ],
        'DATE_ENTERED' => [
            'width' => '10',
            'label' => 'LBL_DATE_ENTERED',
            'default' => true,
        ],
    ];
