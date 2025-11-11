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

global $mod_strings;

$popupMeta = ['moduleMain' => 'Opportunity',
    'varName' => 'OPPORTUNITY',
    'orderBy' => 'name',
    'whereClauses' => ['name' => 'opportunities.name',
        'account_name' => 'accounts.name'],
    'searchInputs' => ['name', 'account_name'],
    'listviewdefs' => [
        'NAME' => [
            'width' => '30',
            'label' => 'LBL_LIST_OPPORTUNITY_NAME',
            'link' => true,
            'default' => true],
        'ACCOUNT_NAME' => [
            'width' => '20',
            'label' => 'LBL_LIST_ACCOUNT_NAME',
            'id' => 'ACCOUNT_ID',
            'module' => 'Accounts',
            'default' => true,
            'sortable' => true,
            'ACLTag' => 'ACCOUNT'],
        'OPPORTUNITY_TYPE' => [
            'width' => '15',
            'default' => true,
            'label' => 'LBL_TYPE'],
        'SALES_STAGE' => [
            'width' => '10',
            'label' => 'LBL_LIST_SALES_STAGE',
            'default' => true],
        'DATE_CLOSED' => [
            'width' => '10',
            'label' => 'LBL_DATE_CLOSED',
            'default' => true],
        'ASSIGNED_USER_NAME' => [
            'width' => '5',
            'label' => 'LBL_LIST_ASSIGNED_USER',
            'default' => true],
        'LOST' => [
            'width' => '10',
            'label' => 'LBL_LOST',
            'default' => true,
        ],
    ],
    'searchdefs' => [
        'name',
        ['name' => 'account_name', 'displayParams' => ['hideButtons' => 'true', 'size' => 30, 'class' => 'sqsEnabled sqsNoAutofill']],
        'opportunity_type',
        'sales_stage',
        'date_closed',
        ['name' => 'assigned_user_id', 'type' => 'enum', 'label' => 'LBL_ASSIGNED_TO', 'function' => ['name' => 'get_user_array', 'params' => [false]]],
        [
            'name' => 'forecasted_likely',
            'comment' => 'Rollup of included RLIs on the Opportunity',
            'readonly' => true,
            'related_fields' => [
                0 => 'currency_id',
                1 => 'base_rate',
            ],
            'label' => 'LBL_FORECASTED_LIKELY',
            'span' => 6,
        ],
        [
            'name' => 'commit_stage',
            'type' => 'enum-cascade',
            'disable_field' => 'closed_won_revenue_line_items',
            'disable_positive' => true,
            'related_fields' => [
                0 => 'probability',
                1 => 'closed_won_revenue_line_items',
            ],
            'span' => 6,
        ],
        [
            'type' => 'currency',
            'readonly' => true,
            'related_fields' => [
                0 => 'currency_id',
                1 => 'base_rate',
            ],
            'label' => 'LBL_LOST',
            'currency_format' => true,
            'width' => 10,
            'name' => 'lost',
        ],
    ],
];
