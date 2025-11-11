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

$popupMeta = [
    'moduleMain' => 'RevenueLineItems',
    'varName' => 'RLI',
    'orderBy' => 'name',
    'whereClauses' => [
        'name' => 'revenue_line_items.name',
        'account_name' => 'accounts.name',
        'opportunity_name' => 'opportunities.name',
    ],
    'searchInputs' => ['name', 'account_name', 'opportunity_name'],
    'listviewdefs' => [
        'NAME' => [
            'width' => '25',
            'label' => 'LBL_NAME',
            'link' => true,
            'default' => true,
        ],
        'OPPORTUNITY_NAME' => [
            'width' => '20',
            'label' => 'LBL_LIST_OPPORTUNITY_NAME',
            'id' => 'OPPORTUNITY_ID',
            'module' => 'Opportunities',
            'link' => true,
            'default' => true,
        ],
        'ACCOUNT_NAME' => [
            'width' => '20',
            'label' => 'LBL_LIST_ACCOUNT_NAME',
            'id' => 'ACCOUNT_ID',
            'module' => 'Accounts',
            'link' => true,
            'default' => true,
        ],
        'LIKELY_CASE' => [
            'width' => '10',
            'default' => true,
            'label' => 'LBL_LIKELY',
        ],
        'ASSIGNED_USER_NAME' => [
            'width' => '10',
            'label' => 'LBL_LIST_ASSIGNED_USER',
            'link' => false,
            'default' => true,
        ],
    ],
    'searchdefs' => [
        'name',
        [
            'name' => 'account_name',
            'displayParams' => ['hideButtons' => 'true', 'size' => 30, 'class' => 'sqsEnabled sqsNoAutofill'],
        ],
        [
            'name' => 'opportunity_name',
            'displayParams' => ['hideButtons' => 'true', 'size' => 30, 'class' => 'sqsEnabled sqsNoAutofill'],
        ],
        [
            'name' => 'assigned_user_id',
            'type' => 'enum',
            'label' => 'LBL_ASSIGNED_TO',
            'function' => ['name' => 'get_user_array', 'params' => [false]],
        ],
    ],
];
