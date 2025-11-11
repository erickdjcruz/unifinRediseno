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


$listViewDefs['Contacts'] = [
    'NAME' => [
        'width' => '20',
        'label' => 'LBL_LIST_NAME',
        'link' => true,
        'contextMenu' => ['objectType' => 'sugarPerson',
            'metaData' => ['contact_id' => '{$ID}',
                'module' => 'Contacts',
                'return_action' => 'ListView',
                'contact_name' => '{$FULL_NAME}',
                'parent_id' => '{$ACCOUNT_ID}',
                'parent_name' => '{$ACCOUNT_NAME}',
                'return_module' => 'Contacts',
                'return_action' => 'ListView',
                'parent_type' => 'Account',
                'notes_parent_type' => 'Account'],
        ],
        'orderBy' => 'name',
        'default' => true,
        'related_fields' => ['first_name', 'last_name', 'salutation', 'account_name', 'account_id'],
    ],
    'TITLE' => [
        'width' => '15',
        'label' => 'LBL_LIST_TITLE',
        'default' => true],
    'ACCOUNT_NAME' => [
        'width' => '34',
        'label' => 'LBL_LIST_ACCOUNT_NAME',
        'module' => 'Accounts',
        'id' => 'ACCOUNT_ID',
        'link' => true,
        'contextMenu' => ['objectType' => 'sugarAccount',
            'metaData' => ['return_module' => 'Contacts',
                'return_action' => 'ListView',
                'module' => 'Accounts',
                'return_action' => 'ListView',
                'parent_id' => '{$ACCOUNT_ID}',
                'parent_name' => '{$ACCOUNT_NAME}',
                'account_id' => '{$ACCOUNT_ID}',
                'account_name' => '{$ACCOUNT_NAME}'],
        ],
        'default' => true,
        'sortable' => true,
        'ACLTag' => 'ACCOUNT',
        'related_fields' => ['account_id']],
    'EMAIL' => [
        'width' => '15',
        'label' => 'LBL_LIST_EMAIL_ADDRESS',
        'sortable' => false,
        'link' => true,
        'customCode' => '{$EMAIL_LINK}{$EMAIL}</a>',
        'default' => true,
    ],
    'PHONE_WORK' => [
        'width' => '15',
        'label' => 'LBL_OFFICE_PHONE',
        'default' => true],
    'DEPARTMENT' => [
        'width' => '10',
        'label' => 'LBL_DEPARTMENT'],
    'DO_NOT_CALL' => [
        'width' => '10',
        'label' => 'LBL_DO_NOT_CALL'],
    'PHONE_HOME' => [
        'width' => '10',
        'label' => 'LBL_HOME_PHONE'],
    'PHONE_MOBILE' => [
        'width' => '10',
        'label' => 'LBL_MOBILE_PHONE'],
    'PHONE_OTHER' => [
        'width' => '10',
        'label' => 'LBL_OTHER_PHONE'],
    'PHONE_FAX' => [
        'width' => '10',
        'label' => 'LBL_FAX_PHONE'],
    'EMAIL2' => [
        'width' => '15',
        'label' => 'LBL_LIST_EMAIL_ADDRESS',
        'sortable' => false,
        'customCode' => '{$EMAIL2_LINK}{$EMAIL2}</a>'],
    'EMAIL_OPT_OUT' => [
        'width' => '10',

        'label' => 'LBL_EMAIL_OPT_OUT'],
    'PRIMARY_ADDRESS_STREET' => [
        'width' => '10',
        'label' => 'LBL_PRIMARY_ADDRESS_STREET'],
    'PRIMARY_ADDRESS_CITY' => [
        'width' => '10',
        'label' => 'LBL_PRIMARY_ADDRESS_CITY'],
    'PRIMARY_ADDRESS_STATE' => [
        'width' => '10',
        'label' => 'LBL_PRIMARY_ADDRESS_STATE'],
    'PRIMARY_ADDRESS_POSTALCODE' => [
        'width' => '10',
        'label' => 'LBL_PRIMARY_ADDRESS_POSTALCODE'],
    'PRIMARY_ADDRESS_COUNTRY' => [
        'width' => '10',
        'label' => 'LBL_PRIMARY_ADDRESS_COUNTRY'],
    'ALT_ADDRESS_STREET' => [
        'width' => '10',
        'label' => 'LBL_ALT_ADDRESS_STREET'],
    'ALT_ADDRESS_CITY' => [
        'width' => '10',
        'label' => 'LBL_ALT_ADDRESS_CITY'],
    'ALT_ADDRESS_STATE' => [
        'width' => '10',
        'label' => 'LBL_ALT_ADDRESS_STATE'],
    'ALT_ADDRESS_POSTALCODE' => [
        'width' => '10',
        'label' => 'LBL_ALT_ADDRESS_POSTALCODE'],
    'ALT_ADDRESS_COUNTRY' => [
        'width' => '10',
        'label' => 'LBL_ALT_ADDRESS_COUNTRY'],
    'CREATED_BY_NAME' => [
        'width' => '10',
        'label' => 'LBL_CREATED'],
    'TEAM_NAME' => [
        'width' => '10',
        'label' => 'LBL_LIST_TEAM',
        'default' => false],
    'ASSIGNED_USER_NAME' => [
        'width' => '10',
        'label' => 'LBL_LIST_ASSIGNED_USER',
        'module' => 'Employees',
        'id' => 'ASSIGNED_USER_ID',
        'default' => true],
    'MODIFIED_BY_NAME' => [
        'width' => '10',
        'label' => 'LBL_MODIFIED'],
    'SYNC_CONTACT' => [
        'type' => 'bool',
        'label' => 'LBL_SYNC_CONTACT',
        'width' => '10',
        'default' => false,
        'sortable' => false,
    ],
    'DATE_ENTERED' => [
        'width' => '10',
        'label' => 'LBL_DATE_ENTERED',
        'default' => true],
];
