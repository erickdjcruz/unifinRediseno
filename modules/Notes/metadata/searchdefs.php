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
$searchdefs ['Notes'] =
    [
        'layout' => [
            'basic_search' => [
                'name' => [
                    'name' => 'name',
                    'default' => true,
                    'width' => '10%',
                ],
                ['name' => 'current_user_only', 'label' => 'LBL_CURRENT_USER_FILTER', 'type' => 'bool'],
                ['name' => 'favorites_only', 'label' => 'LBL_FAVORITES_FILTER', 'type' => 'bool',],
            ],
            'advanced_search' => [
                'name' => [
                    'name' => 'name',
                    'default' => true,
                    'width' => '10%',
                ],
                'contact_name' => [
                    'type' => 'name',
                    'link' => 'contact',
                    'label' => 'LBL_CONTACT_NAME',
                    'width' => '10%',
                    'default' => true,
                    'name' => 'contact_name',
                ],
                'parent_name' => [
                    'type' => 'parent',
                    'label' => 'LBL_RELATED_TO',
                    'width' => '10%',
                    'default' => true,
                    'name' => 'parent_name',
                ],
                'date_entered' => [
                    'type' => 'datetime',
                    'label' => 'LBL_DATE_ENTERED',
                    'width' => '10%',
                    'default' => true,
                    'name' => 'date_entered',
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
