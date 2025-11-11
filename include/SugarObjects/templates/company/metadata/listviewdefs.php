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
$module_name = '<module_name>';
$OBJECT_NAME = '<OBJECT_NAME>';
$listViewDefs[$module_name] = [
    'NAME' => [
        'width' => '40',
        'label' => 'LBL_ACCOUNT_NAME',
        'link' => true,
        'default' => true],
    'BILLING_ADDRESS_CITY' => [
        'width' => '10',
        'label' => 'LBL_CITY',
        'default' => true,
    ],
    'PHONE_OFFICE' => [
        'width' => '10',
        'label' => 'LBL_PHONE',
        'default' => true],
    $OBJECT_NAME . '_TYPE' => [
        'width' => '10',
        'label' => 'LBL_TYPE'],
    'INDUSTRY' => [
        'width' => '10',
        'label' => 'LBL_INDUSTRY'],
    'ANNUAL_REVENUE' => [
        'width' => '10',
        'label' => 'LBL_ANNUAL_REVENUE'],
    'PHONE_FAX' => [
        'width' => '10',
        'label' => 'LBL_PHONE_FAX'],
    'BILLING_ADDRESS_STREET' => [
        'width' => '15',
        'label' => 'LBL_BILLING_ADDRESS_STREET'],
    'BILLING_ADDRESS_STATE' => [
        'width' => '7',
        'label' => 'LBL_BILLING_ADDRESS_STATE'],
    'BILLING_ADDRESS_POSTALCODE' => [
        'width' => '10',
        'label' => 'LBL_BILLING_ADDRESS_POSTALCODE'],
    'BILLING_ADDRESS_COUNTRY' => [
        'width' => '10',
        'label' => 'LBL_BILLING_ADDRESS_COUNTRY'],
    'SHIPPING_ADDRESS_STREET' => [
        'width' => '15',
        'label' => 'LBL_SHIPPING_ADDRESS_STREET'],
    'SHIPPING_ADDRESS_CITY' => [
        'width' => '10',
        'label' => 'LBL_SHIPPING_ADDRESS_CITY'],
    'SHIPPING_ADDRESS_STATE' => [
        'width' => '7',
        'label' => 'LBL_SHIPPING_ADDRESS_STATE'],
    'SHIPPING_ADDRESS_POSTALCODE' => [
        'width' => '10',
        'label' => 'LBL_SHIPPING_ADDRESS_POSTALCODE'],
    'SHIPPING_ADDRESS_COUNTRY' => [
        'width' => '10',
        'label' => 'LBL_SHIPPING_ADDRESS_COUNTRY'],
    'PHONE_ALTERNATE' => [
        'width' => '10',
        'label' => 'LBL_PHONE_ALT'],
    'WEBSITE' => [
        'width' => '10',
        'label' => 'LBL_WEBSITE'],
    'OWNERSHIP' => [
        'width' => '10',
        'label' => 'LBL_OWNERSHIP'],
    'EMPLOYEES' => [
        'width' => '10',
        'label' => 'LBL_EMPLOYEES'],
    'TICKER_SYMBOL' => [
        'width' => '10',
        'label' => 'LBL_TICKER_SYMBOL'],
    'EMAIL1' => [
        'width' => '15',
        'label' => 'LBL_EMAIL_ADDRESS',
        'sortable' => false,
        'link' => true,
        'customCode' => '{$EMAIL1_LINK}{$EMAIL1}</a>',
        'default' => true,
    ],
    'TEAM_NAME' => [
        'width' => '2',
        'label' => 'LBL_TEAM',
        'default' => false],
    'ASSIGNED_USER_NAME' => [
        'width' => '2',
        'label' => 'LBL_ASSIGNED_USER',
        'module' => 'Employees',
        'id' => 'ASSIGNED_USER_ID',
        'default' => true],
];
