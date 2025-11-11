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
$viewdefs['ChangeTimers']['base']['view']['list'] = [
    'panels' => [
        0 =>
            [
                'label' => 'LBL_PANEL_1',
                'fields' => [
                    [
                        'label' => 'LBL_FIELD_NAME',
                        'enabled' => true,
                        'default' => true,
                        'readonly' => true,
                        'name' => 'field_name',
                    ],
                    [
                        'label' => 'LBL_VALUE',
                        'enabled' => true,
                        'default' => true,
                        'name' => 'value_string',
                        'sortable' => false,
                        'readonly' => true,
                    ],
                    [
                        'label' => 'LBL_FROM_DATETIME',
                        'enabled' => true,
                        'default' => true,
                        'name' => 'from_datetime',
                        'readonly' => true,
                    ],
                    [
                        'label' => 'LBL_TO_DATETIME',
                        'enabled' => true,
                        'default' => true,
                        'name' => 'to_datetime',
                        'readonly' => true,
                    ],
                    [
                        'label' => 'LBL_HOURS',
                        'enabled' => true,
                        'default' => true,
                        'name' => 'hours',
                        'sortable' => false,
                        'readonly' => true,
                    ],
                    [
                        'label' => 'LBL_BUSINESS_HOURS',
                        'enabled' => true,
                        'default' => true,
                        'name' => 'business_hours',
                        'sortable' => false,
                        'readonly' => true,
                    ],
                ],
            ],
    ],
];
