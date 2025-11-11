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
$viewdefs['Holidays']['base']['view']['subpanel-list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'default' => true,
                    'label' => 'LBL_LIST_HOLIDAY_DATE',
                    'enabled' => true,
                    'name' => 'holiday_date',
                    'link' => true,
                ],
                [
                    'default' => true,
                    'label' => 'LBL_LIST_HOLIDAY_NAME',
                    'enabled' => true,
                    'name' => 'name',
                    'link' => true,
                ],
                [
                    'default' => true,
                    'label' => 'LBL_LIST_DATE_ENTERED',
                    'enabled' => true,
                    'name' => 'date_entered',
                ],
                [
                    'default' => true,
                    'label' => 'LBL_LIST_DATE_MODIFIED',
                    'enabled' => true,
                    'name' => 'date_modified',
                ],
            ],
        ],
    ],
];
