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


$viewdefs['Cases']['portal']['view']['list'] = [
    'panels' => [
        0 =>
            [
                'label' => 'LBL_PANEL_DEFAULT',
                'fields' => [
                    [
                        'name' => 'case_number',
                        'label' => 'LBL_LIST_NUMBER',
                        'enabled' => true,
                        'default' => true,
                        'readonly' => true,
                    ],
                    [
                        'name' => 'name',
                        'link' => true,
                        'label' => 'LBL_LIST_SUBJECT',
                        'enabled' => true,
                        'default' => true,
                    ],
                    [
                        'name' => 'status',
                        'label' => 'LBL_LIST_STATUS',
                        'enabled' => true,
                        'default' => true,
                    ],
                    [
                        'name' => 'priority',
                        'label' => 'LBL_LIST_PRIORITY',
                        'enabled' => true,
                        'default' => true,
                    ],
                    [
                        'name' => 'type',
                        'label' => 'LBL_TYPE',
                        'enabled' => true,
                        'default' => true,
                    ],
                    [
                        'name' => 'date_entered',
                        'label' => 'LBL_LIST_DATE_CREATED',
                        'enabled' => true,
                        'default' => true,
                        'readonly' => true,
                    ],
                ],
            ],
    ],
    'last_state' => [
        'id' => 'list',
    ],
];
