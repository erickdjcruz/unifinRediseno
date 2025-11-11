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


$listViewDefs['Users'] = [
    'NAME' => [
        'width' => '30',
        'label' => 'LBL_LIST_NAME',
        'link' => true,
        'related_fields' => ['last_name', 'first_name'],
        'orderBy' => 'last_name',
        'default' => true],
    'USER_NAME' => [
        'width' => '5',
        'label' => 'LBL_USER_NAME',
        'link' => true,
        'default' => true],
    'TITLE' => [
        'width' => '15',
        'label' => 'LBL_TITLE',
        'link' => true,
        'default' => true],
    'DEPARTMENT' => [
        'width' => '15',
        'label' => 'LBL_DEPARTMENT',
        'link' => true,
        'default' => true],
    'EMAIL' => [
        'width' => '30',
        'sortable' => false,
        'label' => 'LBL_LIST_EMAIL',
        'link' => true,
        'default' => true],
    'PHONE_WORK' => [
        'width' => '25',
        'label' => 'LBL_LIST_PHONE',
        'link' => true,
        'default' => true],
    'STATUS' => [
        'width' => '10',
        'label' => 'LBL_STATUS',
        'link' => false,
        'default' => true],
    'LICENSE_TYPE' => [
        'width' => '20',
        'label' => 'LBL_LICENSE_TYPE',
        'link' => false,
        'default' => true],
    'IS_ADMIN' => [
        'width' => '10',
        'label' => 'LBL_ADMIN',
        'link' => false,
        'default' => true],
    'IS_GROUP' => [
        'width' => '10',
        'label' => 'LBL_LIST_GROUP',
        'link' => true,
        'default' => false],
];
