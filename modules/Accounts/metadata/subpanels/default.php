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

$subpanel_layout = [
    'top_buttons' => [
        ['widget_class' => 'SubPanelTopCreateButton'],
        ['widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'Accounts'],
    ],

    'where' => '',

    'list_fields' => [
        'name' => [
            'vname' => 'LBL_LIST_ACCOUNT_NAME',
            'widget_class' => 'SubPanelDetailViewLink',
            'width' => '45%',
            'default' => true,
        ],
        'billing_address_city' => [
            'vname' => 'LBL_LIST_CITY',
            'width' => '20%',
            'default' => true,
        ],
        'billing_address_country' => [
            'type' => 'varchar',
            'vname' => 'LBL_BILLING_ADDRESS_COUNTRY',
            'width' => '7%',
            'default' => true,
        ],
        'phone_office' => [
            'vname' => 'LBL_LIST_PHONE',
            'width' => '20%',
            'default' => true,
        ],
        'edit_button' => [
            'vname' => 'LBL_EDIT_BUTTON',
            'widget_class' => 'SubPanelEditButton',
            'width' => '4%',
            'default' => true,
        ],
        'remove_button' => [
            'vname' => 'LBL_REMOVE',
            'widget_class' => 'SubPanelRemoveButtonAccount',
            'width' => '4%',
            'default' => true,
        ],
    ],
];
