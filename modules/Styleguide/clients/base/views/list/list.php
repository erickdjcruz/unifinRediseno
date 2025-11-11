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
$viewdefs['Styleguide']['base']['view']['list'] = [
    'panels' => [
        [
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'fields' => [
                        'salutation',
                        'first_name',
                        'last_name',
                    ],
                    'link' => true,
                    'label' => 'fullname',
                    'enabled' => true,
                    'default' => true,
                    'sortable' => false,
                ],
                [
                    'name' => 'title',
                    'label' => 'text',
                    'sortable' => false,
                ],
                [
                    'name' => 'do_not_call',
                    'label' => 'bool',
                    'sortable' => false,
                ],
                [
                    'name' => 'email',
                    'label' => 'email',
                    'sortable' => false,
                ],
                [
                    'name' => 'assigned_user_name',
                    'label' => 'relate',
                    'id' => 'ASSIGNED_USER_ID',
                    'default' => true,
                    'sortable' => false,
                ],
                [
                    'name' => 'list_price',
                    'label' => 'currency',
                ],
                [
                    'name' => 'birthdate',
                    'label' => 'date',
                    'sortable' => false,
                ],
                [
                    'name' => 'date_end',
                    'label' => 'datetimecombo',
                    'sortable' => false,
                ],
            ],
        ],
    ],
];
