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


$listViewDefs['Documents'] = [
    'DOCUMENT_NAME' => [
        'width' => '20',
        'label' => 'LBL_DOCUMENT_NAME',
        'link' => true,
        'default' => true,
        'bold' => true,
    ],
    'FILENAME' => [
        'width' => '20',
        'label' => 'LBL_FILENAME',
        'link' => true,
        'default' => true,
        'bold' => false,
        'displayParams' => ['module' => 'Documents',],
        'sortable' => false,
        'related_fields' => [
            0 => 'document_revision_id',
            1 => 'doc_id',
            2 => 'doc_type',
            3 => 'doc_url',
        ],
    ],
    'DOC_TYPE' => [
        'width' => '5',
        'label' => 'LBL_DOC_TYPE',
        'link' => false,
        'default' => true,
    ],
    'CATEGORY_ID' => [
        'width' => '10',
        'label' => 'LBL_LIST_CATEGORY',
        'default' => true,
    ],
    'SUBCATEGORY_ID' => [
        'width' => '15',
        'label' => 'LBL_LIST_SUBCATEGORY',
        'default' => true,
    ],
    'TEAM_NAME' => [
        'width' => '2',
        'label' => 'LBL_LIST_TEAM',
        'default' => false,
        'sortable' => false,
    ],
    'LAST_REV_CREATE_DATE' => [
        'width' => '10',
        'label' => 'LBL_LIST_LAST_REV_DATE',
        'default' => true,
        'sortable' => false,
        'module' => 'DocumentRevisions',
        'related_fields' => [
            0 => 'latest_revision_id',
        ],
    ],
    'EXP_DATE' => [
        'width' => '10',
        'label' => 'LBL_LIST_EXP_DATE',
        'default' => true,
    ],
    'ASSIGNED_USER_NAME' => [
        'width' => '10',
        'label' => 'LBL_LIST_ASSIGNED_USER',
        'module' => 'Employees',
        'id' => 'ASSIGNED_USER_ID',
        'default' => true],
    'MODIFIED_BY_NAME' => [
        'width' => '10',
        'label' => 'LBL_MODIFIED_USER',
        'module' => 'Users',
        'id' => 'USERS_ID',
        'default' => false,
        'sortable' => false,
        'related_fields' => [
            0 => 'modified_user_id',
        ],
    ],
    'DATE_ENTERED' => [
        'width' => '10',
        'label' => 'LBL_DATE_ENTERED',
        'default' => true,
    ],
];
