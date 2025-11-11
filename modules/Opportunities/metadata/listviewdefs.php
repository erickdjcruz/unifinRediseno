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


$listViewDefs['Opportunities'] = [
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
        'link' => true,
        'default' => true,
        'sortable' => true,
        'ACLTag' => 'ACCOUNT',
        'contextMenu' => ['objectType' => 'sugarAccount',
            'metaData' => ['return_module' => 'Contacts',
                'return_action' => 'ListView',
                'module' => 'Accounts',
                'return_action' => 'ListView',
                'parent_id' => '{$ACCOUNT_ID}',
                'parent_name' => '{$ACCOUNT_NAME}',
                'account_id' => '{$ACCOUNT_ID}',
                'account_name' => '{$ACCOUNT_NAME}',
            ],
        ],
        'related_fields' => ['account_id']],
    'SALES_STAGE' => [
        'width' => '10',
        'label' => 'LBL_LIST_SALES_STAGE',
        'default' => true],
    'AMOUNT_USDOLLAR' => [
        'width' => '10',
        'label' => 'LBL_LIST_AMOUNT_USDOLLAR',
        'align' => 'right',
        'default' => true,
        'currency_format' => true,
    ],
    'OPPORTUNITY_TYPE' => [
        'width' => '15',
        'label' => 'LBL_TYPE'],
    'LEAD_SOURCE' => [
        'width' => '15',
        'label' => 'LBL_LEAD_SOURCE'],
    'NEXT_STEP' => [
        'width' => '10',
        'label' => 'LBL_NEXT_STEP'],
    'PROBABILITY' => [
        'width' => '10',
        'label' => 'LBL_PROBABILITY'],
    'DATE_CLOSED' => [
        'width' => '10',
        'label' => 'LBL_DATE_CLOSED',
        'default' => true],
    'CREATED_BY_NAME' => [
        'width' => '10',
        'label' => 'LBL_CREATED'],
    'TEAM_NAME' => [
        'width' => '5',
        'label' => 'LBL_LIST_TEAM',
        'default' => false],
    'ASSIGNED_USER_NAME' => [
        'width' => '5',
        'label' => 'LBL_LIST_ASSIGNED_USER',
        'module' => 'Employees',
        'id' => 'ASSIGNED_USER_ID',
        'default' => true],
    'MODIFIED_BY_NAME' => [
        'width' => '5',
        'label' => 'LBL_MODIFIED'],
    'DATE_ENTERED' => [
        'width' => '10',
        'label' => 'LBL_DATE_ENTERED',
        'default' => true],
];
