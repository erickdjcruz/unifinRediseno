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
    'moduleMain' => 'Bug',
    'varName' => 'BUG',
    'orderBy' => 'bugs.name',
    'whereClauses' => [
        'name' => 'bugs.name',
        'bug_number' => 'bugs.bug_number',
    ],
    'listviewdefs' => [
        'BUG_NUMBER' => [
            'width' => '5',
            'label' => 'LBL_LIST_NUMBER',
            'link' => true,
            'default' => true],
        'NAME' => [
            'width' => '32',
            'label' => 'LBL_LIST_SUBJECT',
            'default' => true,
            'link' => true],
        'PRIORITY' => [
            'width' => '10',
            'label' => 'LBL_LIST_PRIORITY',
            'default' => true],
        'STATUS' => [
            'width' => '10',
            'label' => 'LBL_LIST_STATUS',
            'default' => true],
        'TYPE' => [
            'width' => '10',
            'label' => 'LBL_LIST_TYPE',
            'default' => true],
        'PRODUCT_CATEGORY' => [
            'width' => '10',
            'label' => 'LBL_PRODUCT_CATEGORY',
            'default' => true],
        'ASSIGNED_USER_NAME' => [
            'width' => '9',
            'label' => 'LBL_LIST_ASSIGNED_USER',
            'default' => true],

    ],
    'searchdefs' => [
        'bug_number',
        'name',
        'priority',
        'status',
        'type',
        'product_category',
        ['name' => 'assigned_user_id', 'type' => 'enum', 'label' => 'LBL_ASSIGNED_TO', 'function' => ['name' => 'get_user_array', 'params' => [false]]],
    ],
];
