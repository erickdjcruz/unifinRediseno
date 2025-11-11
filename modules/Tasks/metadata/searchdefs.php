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
$searchdefs ['Tasks'] =
    [
        'layout' => [
            'basic_search' => [
                'name' => [
                    'name' => 'name',
                    'default' => true,
                    'width' => '10%',
                ],
                'current_user_only' => [
                    'name' => 'current_user_only',
                    'label' => 'LBL_CURRENT_USER_FILTER',
                    'type' => 'bool',
                    'default' => true,
                    'width' => '10%',
                ],
                ['name' => 'favorites_only', 'label' => 'LBL_FAVORITES_FILTER', 'type' => 'bool',],
                ['name' => 'open_only', 'label' => 'LBL_OPEN_ITEMS', 'type' => 'bool', 'default' => false, 'width' => '10%'],
            ],
            'advanced_search' => [
                'name' => [
                    'name' => 'name',
                    'default' => true,
                    'width' => '10%',
                ],
                'contact_name' => [
                    'name' => 'contact_name',
                    'label' => 'LBL_CONTACT_NAME',
                    'type' => 'name',
                    'default' => true,
                    'width' => '10%',
                ],
                'current_user_only' => [
                    'name' => 'current_user_only',
                    'label' => 'LBL_CURRENT_USER_FILTER',
                    'type' => 'bool',
                    'default' => true,
                    'width' => '10%',
                ],
                'status' => [
                    'name' => 'status',
                    'default' => true,
                    'width' => '10%',
                ],
                'parent_name' => [
                    'type' => 'parent',
                    'label' => 'LBL_LIST_RELATED_TO',
                    'width' => '10%',
                    'default' => true,
                    'name' => 'parent_name',
                ],
                'assigned_user_id' => [
                    'name' => 'assigned_user_id',
                    'type' => 'enum',
                    'label' => 'LBL_ASSIGNED_TO',
                    'function' => [
                        'name' => 'get_user_array',
                        'params' => [
                            0 => false,
                        ],
                    ],
                    'default' => true,
                    'width' => '10%',
                ],

                ['name' => 'favorites_only', 'label' => 'LBL_FAVORITES_FILTER', 'type' => 'bool',],
            ],
        ],
        'templateMeta' => [
            'maxColumns' => '3',
            'maxColumnsBasic' => '4',
            'widths' => [
                'label' => '10',
                'field' => '30',
            ],
        ],
    ];
