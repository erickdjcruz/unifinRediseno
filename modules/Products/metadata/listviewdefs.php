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


$listViewDefs['Products'] = [
    'NAME' => [
        'width' => '40',
        'label' => 'LBL_LIST_NAME',
        'link' => true,
        'default' => true],
    'ACCOUNT_NAME' => [
        'width' => '20',
        'label' => 'LBL_LIST_ACCOUNT_NAME',
        'id' => 'ACCOUNT_ID',
        'module' => 'Accounts',
        'link' => true,
        'default' => true,
        'ACLTag' => 'ACCOUNT',
        'related_fields' => ['account_id'],
        'sortable' => true],
    'STATUS' => [
        'width' => '10',
        'label' => 'LBL_LIST_STATUS',
        'link' => false,
        'default' => true],
    'QUANTITY' => [
        'width' => '10',
        'label' => 'LBL_LIST_QUANTITY',
        'link' => false,
        'default' => true],
    'DISCOUNT_USDOLLAR' => [
        'width' => '10',
        'label' => 'LBL_LIST_DISCOUNT_PRICE',
        'link' => false,
        'default' => true,
        'currency_format' => true,
        'align' => 'right'],
    'LIST_USDOLLAR' => [
        'width' => '10',
        'label' => 'LBL_LIST_LIST_PRICE',
        'link' => false,
        'default' => true,
        'currency_format' => true,
        'align' => 'right',
    ],
    'DATE_PURCHASED' => [
        'width' => '10',
        'label' => 'LBL_LIST_DATE_PURCHASED',
        'link' => false,
        'default' => true],
    'DATE_SUPPORT_EXPIRES' => [
        'width' => '10',
        'label' => 'LBL_LIST_SUPPORT_EXPIRES',
        'link' => false,
        'default' => true],
    'CATEGORY_NAME' => [
        'type' => 'relate',
        'link' => 'product_categories_link',
        'label' => 'LBL_CATEGORY_NAME',
        'width' => '10',
        'default' => false],
    'CONTACT_NAME' => [
        'type' => 'relate',
        'link' => 'contact_link',
        'label' => 'LBL_CONTACT_NAME',
        'width' => '10',
        'default' => false],
    'QUOTE_NAME' => [
        'type' => 'relate',
        'link' => 'quotes',
        'label' => 'LBL_QUOTE_NAME',
        'width' => '10',
        'default' => false],
    'TYPE_NAME' => [
        'type' => 'varchar',
        'label' => 'LBL_TYPE',
        'width' => '10',
        'default' => false],
    'SERIAL_NUMBER' => [
        'type' => 'varchar',
        'label' => 'LBL_SERIAL_NUMBER',
        'width' => '10',
        'default' => false],
    'TEAM_NAME' => [
        'width' => '2',
        'label' => 'LBL_LIST_TEAM',
        'default' => false],
    'ASSIGNED_USER_NAME' => [
        'width' => '8',
        'label' => 'LBL_LIST_ASSIGNED_USER',
        'module' => 'Employees',
        'id' => 'ASSIGNED_USER_ID',
        'default' => true],
    'DATE_ENTERED' => [
        'type' => 'datetime',
        'label' => 'LBL_DATE_ENTERED',
        'width' => '10',
        'default' => true],
];
