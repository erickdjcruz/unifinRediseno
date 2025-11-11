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
$viewdefs['Contracts']['DetailView'] = [
    'templateMeta' => [
        'maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
        'form' => [
            'buttons' => [
                'EDIT',
                'SHARE',
                'DUPLICATE',
                'DELETE',
            ],
        ],
    ],
    'panels' => [
        'lbl_contract_information' => [
            [
                [
                    'name' => 'name',
                    'label' => 'LBL_CONTRACT_NAME',
                ],
                'status',
            ],
            [
                'reference_code',
                'start_date',
            ],
            [
                'account_name',
                'end_date',
            ],
            [
                [
                    'name' => 'opportunity_name',
                    'label' => 'LBL_OPPORTUNITY',
                ],
            ],
            [
                [
                    'name' => 'type',
                    'label' => 'LBL_CONTRACT_TYPE',
                ],
                [
                    'name' => 'contract_term',
                    'customCode' => '{$fields.contract_term.value}&nbsp;{if !empty($fields.contract_term.value) }{$MOD.LBL_DAYS}{/if}',
                    'label' => 'LBL_CONTRACT_TERM',
                ],
            ],
            [
                [
                    'name' => 'total_contract_value',
                    'label' => '{$MOD.LBL_TOTAL_CONTRACT_VALUE} ({$fields.currency_name.value})',
                ],
                'company_signed_date',
            ],
            [

                'expiration_notice',
                'customer_signed_date',
            ],
            [
                'description',
            ],
        ],
        'LBL_PANEL_ASSIGNMENT' => [
            [
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO',
                ],
                [
                    'name' => 'date_modified',
                    'customCode' => '{$fields.date_modified.value}&nbsp;{$APP.LBL_BY}&nbsp;{$fields.modified_by_name.value}',
                    'label' => 'LBL_DATE_MODIFIED',
                ],
            ],
            [
                'team_name',
                [
                    'name' => 'date_entered',
                    'customCode' => '{$fields.date_entered.value}&nbsp;{$APP.LBL_BY}&nbsp;{$fields.created_by_name.value}',
                    'label' => 'LBL_DATE_ENTERED',
                ],
            ],
        ],
    ],
];
