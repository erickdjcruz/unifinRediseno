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
$listViewDefs ['Notes'] =
    [
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
            'related_fields' => [
                0 => 'contact_id',
            ],
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

        'FILENAME' => [
            'width' => '20',
            'label' => 'LBL_LIST_FILENAME',
            'default' => true,
            'type' => 'file',
            'related_fields' => [
                0 => 'file_url',
                1 => 'id',
            ],
            'displayParams' => [
                'module' => 'Notes',
            ],
        ],
        'CREATED_BY_NAME' => [
            'type' => 'relate',
            'label' => 'LBL_CREATED_BY',
            'width' => '10',
            'default' => true,
            'related_fields' => ['created_by'],
        ],
        'DATE_MODIFIED' => [
            'width' => '20',
            'label' => 'LBL_DATE_MODIFIED',
            'link' => false,
            'default' => false,
        ],
        'TEAM_NAME' => [
            'width' => '2',
            'label' => 'LBL_LIST_TEAM',
            'default' => false,
        ],
        'DATE_ENTERED' => [
            'type' => 'datetime',
            'label' => 'LBL_DATE_ENTERED',
            'width' => '10',
            'default' => true,
        ],
    ];
