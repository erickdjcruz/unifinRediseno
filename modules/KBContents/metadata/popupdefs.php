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
    'moduleMain' => 'KBContents',
    'varName' => 'KBContents',
    'orderBy' => 'kbcontents.name',
    'whereClauses' => [
        'name' => 'kbcontents.name',
        'my_favorite' => 'kbcontents.my_favorite',
        'status' => 'kbcontents.status',
    ],
    'searchInputs' => [
        0 => 'name',
        1 => 'status',
        2 => 'my_favorite',
    ],
    'searchdefs' => [
        'name' => [
            'type' => 'name',
            'link' => true,
            'label' => 'LBL_NAME',
            'width' => '10%',
            'name' => 'name',
        ],
        'my_favorite' => [
            'type' => 'bool',
            'studio' => [
                'list' => false,
                'recordview' => false,
            ],
            'link' => 'favorite_link',
            'label' => 'LBL_FAVORITE',
            'width' => '10%',
            'name' => 'my_favorite',
        ],
        'status' => [
            'type' => 'enum',
            'studio' => true,
            'label' => 'LBL_STATUS',
            'width' => '10%',
            'name' => 'status',
        ],
    ],
    'listviewdefs' => [
        'name' => [
            'label' => 'LBL_NAME',
            'default' => true,
            'link' => true,
            'width' => '10%',
            'name' => 'name',
        ],
        'my_favorite' => [
            'type' => 'bool',
            'studio' => [
                'list' => false,
                'recordview' => false,
            ],
            'link' => 'favorite_link',
            'label' => 'LBL_FAVORITE',
            'width' => '10%',
            'default' => true,
        ],
        'status' => [
            'label' => 'LBL_STATUS',
            'default' => true,
            'type' => 'status',
            'width' => '10%',
            'name' => 'status',
        ],
    ],
];
