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

$popupMeta = [
    'moduleMain' => 'Tag',
    'varName' => 'TAG',
    'className' => 'Tag',
    'orderBy' => 'name',
    'whereClauses' => ['name' => 'tags.name'],
    'listviewdefs' => [
        'NAME' => [
            'width' => '20',
            'label' => 'LBL_NAME',
            'link' => true,
            'default' => true,
        ],
        'DATE_ENTERED' => [
            'width' => '10',
            'label' => 'LBL_DATE_ENTERED',
            'default' => true,
        ],
        'DATE_MODIFIED' => [
            'width' => '10',
            'label' => 'LBL_DATE_MODIFIED',
            'default' => true,
        ],
        'MODIFIED_USER_ID' => [
            'width' => '10',
            'label' => 'LBL_MODIFIED_ID',
            'default' => true,
        ],
        'CREATED_BY' => [
            'width' => '10',
            'label' => 'LBL_CREATED',
            'default' => true,
        ],
        'DESCRIPTION' => [
            'width' => '10',
            'label' => 'LBL_DESCRIPTION',
            'default' => false,
        ],
        'DELETED' => [
            'width' => '10',
            'label' => 'LBL_DELETED',
            'default' => false,
        ],
        'SOURCE_ID' => [
            'width' => '10',
            'label' => 'LBL_SOURCE_ID',
            'default' => false,
        ],
        'SOURCE_TYPE' => [
            'width' => '10',
            'label' => 'LBL_SOURCE_TYPE',
            'default' => false,
        ],
        'SOURCE_META' => [
            'width' => '15',
            'label' => 'LBL_SOURCE_META',
            'default' => false,
        ],
        'ASSIGNED_USER_ID' => [
            'width' => '7',
            'label' => 'LBL_ASSIGNED_TO_ID',
            'default' => false,
        ],
    ],
    'searchdefs' => [
        'name',
        [
            'name' => 'assigned_user_id',
            'type' => 'enum',
            'label' => 'LBL_ASSIGNED_TO',
            'function' => [
                'name' => 'get_user_array',
                'params' => [false],
            ],
        ],
    ],
];
