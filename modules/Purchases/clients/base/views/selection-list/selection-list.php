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

$viewdefs['Purchases']['base']['view']['selection-list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'link' => true,
                    'label' => 'LBL_NAME',
                    'enabled' => true,
                    'default' => true,
                    'width' => 'large',
                ],
                [
                    'name' => 'account_name',
                    'label' => 'LBL_ACCOUNT_NAME',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'product_template_name',
                    'label' => 'LBL_PRODUCT_TEMPLATE_NAME',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'start_date',
                    'label' => 'LBL_START_DATE',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'end_date',
                    'label' => 'LBL_END_DATE',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'service',
                    'label' => 'LBL_SERVICE',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_LIST_ASSIGNED_USER',
                    'id' => 'ASSIGNED_USER_ID',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'renewable',
                    'label' => 'LBL_RENEWABLE',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'category_name',
                    'label' => 'LBL_CATEGORY_NAME',
                    'id' => 'CATEGORY_ID',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'type_name',
                    'label' => 'LBL_PRODUCT_TYPE',
                    'id' => 'TYPE_ID',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'date_modified',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'date_entered',
                    'type' => 'datetime',
                    'label' => 'LBL_DATE_ENTERED',
                    'enabled' => true,
                    'default' => false,
                    'readonly' => true,
                ],
            ],
        ],
    ],
];
