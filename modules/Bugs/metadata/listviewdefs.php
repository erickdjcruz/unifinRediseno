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


$listViewDefs['Bugs'] = [
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
    'STATUS' => [
        'width' => '10',
        'label' => 'LBL_LIST_STATUS',
        'default' => true],
    'TYPE' => [
        'width' => '10',
        'label' => 'LBL_LIST_TYPE',
        'default' => true],
    'PRIORITY' => [
        'width' => '10',
        'label' => 'LBL_LIST_PRIORITY',
        'default' => true],
    'RELEASE_NAME' => [
        'width' => '10',
        'label' => 'LBL_FOUND_IN_RELEASE',
        'default' => false,
        'related_fields' => ['found_in_release'],
        'module' => 'Releases',
        'id' => 'FOUND_IN_RELEASE',],
    'FIXED_IN_RELEASE_NAME' => [
        'width' => '10',
        'label' => 'LBL_LIST_FIXED_IN_RELEASE',
        'default' => true,
        'related_fields' => ['fixed_in_release'],
        'module' => 'Releases',
        'id' => 'FIXED_IN_RELEASE',],
    'RESOLUTION' => [
        'width' => '10',
        'label' => 'LBL_LIST_RESOLUTION',
        'default' => false],
    'TEAM_NAME' => [
        'width' => '9',
        'label' => 'LBL_LIST_TEAM',
        'default' => false],
    'ASSIGNED_USER_NAME' => [
        'width' => '9',
        'label' => 'LBL_LIST_ASSIGNED_USER',
        'module' => 'Employees',
        'id' => 'ASSIGNED_USER_ID',
        'default' => true],
];
