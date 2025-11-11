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

$viewdefs['Purchases']['base']['view']['subpanel-list'] = [
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
                ],
                [
                    'name' => 'account_name',
                    'link' => true,
                    'label' => 'LBL_ACCOUNT_NAME',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'product_template_name',
                    'link' => true,
                    'label' => 'LBL_PRODUCT_TEMPLATE_NAME',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'total_quantity',
                    'label' => 'LBL_TOTAL_QUANTITY',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'total_revenue',
                    'label' => 'LBL_TOTAL_REVENUE',
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
            ],
        ],
    ],
];
