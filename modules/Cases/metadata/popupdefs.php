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
    'moduleMain' => 'Case',
    'varName' => 'CASE',
    'className' => 'aCase',
    'orderBy' => 'name',
    'whereClauses' => ['name' => 'cases.name',
        'case_number' => 'cases.case_number',
        'account_name' => 'accounts.name'],
    'listviewdefs' => [
        'CASE_NUMBER' => [
            'width' => '5',
            'label' => 'LBL_LIST_NUMBER',
            'default' => true],
        'NAME' => [
            'width' => '35',
            'label' => 'LBL_LIST_SUBJECT',
            'link' => true,
            'default' => true],
        'ACCOUNT_NAME' => [
            'width' => '25',
            'label' => 'LBL_LIST_ACCOUNT_NAME',
            'module' => 'Accounts',
            'id' => 'ACCOUNT_ID',
            'link' => true,
            'default' => true,
            'ACLTag' => 'ACCOUNT',
            'related_fields' => ['account_id']],
        'PRIORITY' => [
            'width' => '8',
            'label' => 'LBL_LIST_PRIORITY',
            'default' => true],
        'STATUS' => [
            'width' => '8',
            'label' => 'LBL_LIST_STATUS',
            'default' => true],
        'ASSIGNED_USER_NAME' => [
            'width' => '2',
            'label' => 'LBL_LIST_ASSIGNED_USER',
            'default' => true,
        ],
    ],
    'searchdefs' => [
        'case_number',
        'name',
        ['name' => 'account_name', 'displayParams' => ['hideButtons' => 'true', 'size' => 30, 'class' => 'sqsEnabled sqsNoAutofill']],
        'priority',
        'status',
        ['name' => 'assigned_user_id', 'type' => 'enum', 'label' => 'LBL_ASSIGNED_TO', 'function' => ['name' => 'get_user_array', 'params' => [false]]],
    ],
];
