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

    'fill_in_additional_fields' => true,

    'list_fields' => [
        'name' => [
            'vname' => 'LBL_LIST_NAME',
            'widget_class' => 'SubPanelDetailViewLink',
            'width' => '28%',
            'sort_by' => 'products.name',
        ],
        'status' => [
            'vname' => 'LBL_LIST_STATUS',
            'width' => '8%',
        ],
        'account_name' => [
            'vname' => 'LBL_LIST_ACCOUNT_NAME',
            'widget_class' => 'SubPanelDetailViewLink',
            'target_record_key' => 'account_id',
            'target_module' => 'Accounts',
            'module' => 'Accounts',
            'width' => '15%',
            'sortable' => false,
        ],
        'contact_name' => [
            'vname' => 'LBL_LIST_CONTACT_NAME',
            'widget_class' => 'SubPanelDetailViewLink',
            'target_record_key' => 'contact_id',
            'target_module' => 'Contacts',
            'module' => 'Contacts',
            'width' => '15%',
            'sortable' => false,
        ],
        'date_purchased' => [
            'vname' => 'LBL_LIST_DATE_PURCHASED',
            'width' => '10%',
        ],
        'discount_price' => [
            'vname' => 'LBL_LIST_DISCOUNT_PRICE',
            'width' => '10%',
        ],
        'date_support_expires' => [
            'vname' => 'LBL_LIST_SUPPORT_EXPIRES',
            'width' => '10%',
        ],
        'edit_button' => [
            'vname' => 'LBL_EDIT_BUTTON',
            'widget_class' => 'SubPanelEditButton',
            'module' => 'Products',
            'width' => '4%',
        ],
        'remove_button' => [
            'vname' => 'LBL_REMOVE',
            'widget_class' => 'SubPanelRemoveButton',
            'module' => 'Leads',
            'width' => '4%',
        ],
        'remove_button' => [
            'vname' => 'LBL_REMOVE',
            'widget_class' => 'SubPanelRemoveButton',
            'module' => 'Leads',
            'width' => '4%',
        ],
        'discount_usdollar' => [
            'usage' => 'query_only',
        ],
    ],
];
