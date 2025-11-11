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
$viewdefs['Contracts']['base']['view']['selection-list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'label' => 'LBL_LIST_NAME',
                    'enabled' => true,
                    'default' => true,
                    'link' => true,
                ],
                [
                    'name' => 'account_name',
                    'target_record_key' => 'account_id',
                    'target_module' => 'Accounts',
                    'label' => 'LBL_LIST_ACCOUNT_NAME',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'start_date',
                    'label' => 'LBL_LIST_START_DATE',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'end_date',
                    'label' => 'LBL_LIST_END_DATE',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'status',
                    'label' => 'LBL_LIST_STATUS',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'total_contract_value',
                    'label' => 'LBL_LIST_CONTRACT_VALUE',
                    'enabled' => true,
                    'default' => false,
                ],
            ],
        ],
    ],
];
