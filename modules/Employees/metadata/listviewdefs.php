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


$listViewDefs['Employees'] = [
    'NAME' => [
        'width' => '20',
        'label' => 'LBL_LIST_NAME',
        'link' => true,
        'related_fields' => ['last_name', 'first_name'],
        'orderBy' => 'last_name',
        'default' => true],
    'DEPARTMENT' => [
        'width' => '10',
        'label' => 'LBL_DEPARTMENT',
        'link' => true,
        'default' => true],
    'TITLE' => [
        'width' => '15',
        'label' => 'LBL_TITLE',
        'link' => true,
        'default' => true],
    'REPORTS_TO_NAME' => [
        'width' => '15',
        'label' => 'LBL_LIST_REPORTS_TO_NAME',
        'link' => true,
        'sortable' => false,
        'default' => true],
    'EMAIL' => [
        'width' => '15',
        'label' => 'LBL_LIST_EMAIL',
        'link' => true,
        'customCode' => '{$EMAIL_LINK}{$EMAIL}</a>',
        'default' => true,
        'sortable' => false],
    'PHONE_WORK' => [
        'width' => '10',
        'label' => 'LBL_LIST_PHONE',
        'link' => true,
        'default' => true],
    'EMPLOYEE_STATUS' => [
        'width' => '10',
        'label' => 'LBL_LIST_EMPLOYEE_STATUS',
        'link' => false,
        'default' => true],
    'DATE_ENTERED' => [
        'width' => '10',
        'label' => 'LBL_DATE_ENTERED',
        'default' => true],
];
