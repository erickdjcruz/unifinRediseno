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
$viewdefs['Employees']['base']['view']['selection-list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'label' => 'LBL_NAME',
                    'enabled' => true,
                    'default' => true,
                    'sortable' => true,
                ],
                [
                    'name' => 'department',
                    'label' => 'LBL_DEPARTMENT',
                    'enabled' => true,
                    'default' => true,
                    'sortable' => true,
                ],
                [
                    'name' => 'title',
                    'label' => 'LBL_TITLE',
                    'enabled' => true,
                    'default' => true,
                    'sortable' => true,
                ],
                [
                    'name' => 'reports_to_name',
                    'label' => 'LBL_REPORTS_TO_NAME',
                    'enabled' => true,
                    'default' => true,
                    'sortable' => true,
                ],
                [
                    'name' => 'email',
                    'label' => 'LBL_EMAIL',
                    'enabled' => true,
                    'default' => true,
                    'sortable' => true,
                ],
                [
                    'name' => 'phone_work',
                    'label' => 'LBL_OFFICE_PHONE',
                    'enabled' => true,
                    'default' => true,
                    'sortable' => true,
                ],
                [
                    'name' => 'employee_status',
                    'label' => 'LBL_EMPLOYEE_STATUS',
                    'enabled' => true,
                    'default' => true,
                    'sortable' => true,
                ],
                [
                    'name' => 'date_entered',
                    'label' => 'LBL_DATE_ENTERED',
                    'enabled' => true,
                    'default' => true,
                    'sortable' => true,
                ],
            ],
        ],
    ],
];
