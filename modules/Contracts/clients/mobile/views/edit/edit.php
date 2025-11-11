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
$viewdefs['Contracts']['mobile']['view']['edit'] = [
    'templateMeta' => [
        'maxColumns' => '1',
        'widths' => [
            ['label' => '10', 'field' => '30'],
        ],
    ],
    'panels' => [
        [
            'fields' => [
                [
                    'name' => 'name',
                    'required' => true,
                ],
                'reference_code',
                [
                    'name' => 'account_name',
                    'required' => true,
                ],
                'opportunity_name',
                'type_name',
                'total_contract_value',
                'expiration_notice',
                'description',
                'tag',
                'assigned_user_name',
                'team_name',
                'status',
                'start_date',
                'end_date',
                'contract_term',
                'company_signed_date',
                'customer_signed_date',
                'date_modified',
                'date_entered',
            ],
        ],
    ],
];
