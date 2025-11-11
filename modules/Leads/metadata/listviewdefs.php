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


$listViewDefs ['Leads'] =
    [
        'NAME' => [
            'width' => '10',
            'label' => 'LBL_LIST_NAME',
            'link' => true,
            'orderBy' => 'name',
            'default' => true,
            'related_fields' => [
                0 => 'first_name',
                1 => 'last_name',
                2 => 'salutation',
            ],
        ],
        'STATUS' => [
            'width' => '7',
            'label' => 'LBL_LIST_STATUS',
            'default' => true,
        ],
        'ACCOUNT_NAME' => [
            'width' => '15',
            'label' => 'LBL_LIST_ACCOUNT_NAME',
            'default' => true,
            'related_fields' => [
                0 => 'account_id',
            ],
        ],
        'PHONE_WORK' => [
            'width' => '15',
            'label' => 'LBL_LIST_PHONE',
            'default' => true,
        ],
        'EMAIL' => [
            'width' => '16',
            'label' => 'LBL_LIST_EMAIL_ADDRESS',
            'sortable' => false,
            'customCode' => '{$EMAIL_LINK}{$EMAIL}</a>',
            'default' => true,
        ],
        'ASSIGNED_USER_NAME' => [
            'width' => '5',
            'label' => 'LBL_LIST_ASSIGNED_USER',
            'module' => 'Employees',
            'id' => 'ASSIGNED_USER_ID',
            'default' => true,
        ],
        'TITLE' => [
            'width' => '10',
            'label' => 'LBL_TITLE',
            'default' => false,
        ],
        'REFERED_BY' => [
            'width' => '10',
            'label' => 'LBL_REFERED_BY',
            'default' => false,
        ],
        'LEAD_SOURCE' => [
            'width' => '10',
            'label' => 'LBL_LEAD_SOURCE',
            'default' => false,
        ],
        'DEPARTMENT' => [
            'width' => '10',
            'label' => 'LBL_DEPARTMENT',
            'default' => false,
        ],
        'DO_NOT_CALL' => [
            'width' => '10',
            'label' => 'LBL_DO_NOT_CALL',
            'default' => false,
        ],
        'PHONE_HOME' => [
            'width' => '10',
            'label' => 'LBL_HOME_PHONE',
            'default' => false,
        ],
        'PHONE_MOBILE' => [
            'width' => '10',
            'label' => 'LBL_MOBILE_PHONE',
            'default' => false,
        ],
        'PHONE_OTHER' => [
            'width' => '10',
            'label' => 'LBL_OTHER_PHONE',
            'default' => false,
        ],
        'PHONE_FAX' => [
            'width' => '10',
            'label' => 'LBL_FAX_PHONE',
            'default' => false,
        ],
        'PRIMARY_ADDRESS_COUNTRY' => [
            'width' => '10',
            'label' => 'LBL_PRIMARY_ADDRESS_COUNTRY',
            'default' => false,
        ],
        'PRIMARY_ADDRESS_STREET' => [
            'width' => '10',
            'label' => 'LBL_PRIMARY_ADDRESS_STREET',
            'default' => false,
        ],
        'PRIMARY_ADDRESS_CITY' => [
            'width' => '10',
            'label' => 'LBL_PRIMARY_ADDRESS_CITY',
            'default' => false,
        ],
        'PRIMARY_ADDRESS_STATE' => [
            'width' => '10',
            'label' => 'LBL_PRIMARY_ADDRESS_STATE',
            'default' => false,
        ],
        'PRIMARY_ADDRESS_POSTALCODE' => [
            'width' => '10',
            'label' => 'LBL_PRIMARY_ADDRESS_POSTALCODE',
            'default' => false,
        ],
        'ALT_ADDRESS_COUNTRY' => [
            'width' => '10',
            'label' => 'LBL_ALT_ADDRESS_COUNTRY',
            'default' => false,
        ],
        'ALT_ADDRESS_STREET' => [
            'width' => '10',
            'label' => 'LBL_ALT_ADDRESS_STREET',
            'default' => false,
        ],
        'ALT_ADDRESS_CITY' => [
            'width' => '10',
            'label' => 'LBL_ALT_ADDRESS_CITY',
            'default' => false,
        ],
        'ALT_ADDRESS_STATE' => [
            'width' => '10',
            'label' => 'LBL_ALT_ADDRESS_STATE',
            'default' => false,
        ],
        'ALT_ADDRESS_POSTALCODE' => [
            'width' => '10',
            'label' => 'LBL_ALT_ADDRESS_POSTALCODE',
            'default' => false,
        ],
        'CREATED_BY' => [
            'width' => '10',
            'label' => 'LBL_CREATED',
            'default' => false,
        ],
        'TEAM_NAME' => [
            'width' => '5',
            'label' => 'LBL_LIST_TEAM',
            'default' => false,
        ],
        'MODIFIED_BY_NAME' => [
            'width' => '5',
            'label' => 'LBL_MODIFIED',
            'default' => false,
        ],
        'DATE_ENTERED' => [
            'width' => '10',
            'label' => 'LBL_DATE_ENTERED',
            'default' => true,
        ],
    ];
