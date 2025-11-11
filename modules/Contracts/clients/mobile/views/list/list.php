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
/*********************************************************************************
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
$viewdefs['Contracts']['mobile']['view']['list'] = [
    'panels' => [
        [
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => [
                [
                    'name' => 'reference_code',
                    'label' => 'LBL_REFERENCE_CODE',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'name',
                    'label' => 'LBL_CONTRACT_NAME',
                    'default' => true,
                    'enabled' => true,
                    'link' => true,
                ],
                [
                    'name' => 'account_name',
                    'label' => 'LBL_ACCOUNT_NAME',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'status',
                    'label' => 'LBL_STATUS',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'start_date',
                    'label' => 'LBL_START_DATE',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'end_date',
                    'label' => 'LBL_END_DATE',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'total_contract_value',
                    'label' => 'LBL_LIST_CONTRACT_VALUE',
                    'related_fields' => [
                        'total_contract_value',
                        'currency_id',
                        'base_rate',
                    ],
                    'currency_field' => 'currency_id',
                    'base_rate_field' => 'base_rate',
                    'enabled' => true,
                    'default' => true,
                ],
            ],
        ],
    ],
];
