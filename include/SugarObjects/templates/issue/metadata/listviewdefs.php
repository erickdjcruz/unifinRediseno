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
    $OBJECT_NAME . '_NUMBER' => [
        'width' => '5',
        'label' => 'LBL_NUMBER',
        'link' => true,
        'default' => true],
    'NAME' => [
        'width' => '32',
        'label' => 'LBL_SUBJECT',
        'default' => true,
        'link' => true],
    'STATUS' => [
        'width' => '10',
        'label' => 'LBL_STATUS',
        'default' => true],
    'PRIORITY' => [
        'width' => '10',
        'label' => 'LBL_PRIORITY',
        'default' => true],
    'RESOLUTION' => [
        'width' => '10',
        'label' => 'LBL_RESOLUTION',
        'default' => true],
    'TEAM_NAME' => [
        'width' => '9',
        'label' => 'LBL_TEAM',
        'default' => false],
    'ASSIGNED_USER_NAME' => [
        'width' => '9',
        'label' => 'LBL_ASSIGNED_USER',
        'module' => 'Employees',
        'id' => 'ASSIGNED_USER_ID',
        'default' => true],

];
