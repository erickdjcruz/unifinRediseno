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
        ['widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'Quotes'],
    ],
    'list_fields' => [
        'name' => [
            'vname' => 'LBL_LIST_QUOTE_NAME',
            'widget_class' => 'SubPanelDetailViewLink',
            'width' => '40%',
        ],
        'total_usdollar' => [
            'vname' => 'LBL_LIST_AMOUNT_USDOLLAR',
            'width' => '15%',
        ],
        'date_quote_expected_closed' => [
            'name' => 'date_quote_expected_closed',
            'vname' => 'LBL_LIST_DATE_QUOTE_EXPECTED_CLOSED',
            'width' => '15%',
        ],
        'assigned_user_name' => [
            'name' => 'assigned_user_name',
            'vname' => 'LBL_LIST_ASSIGNED_TO_NAME',
            'widget_class' => 'SubPanelDetailViewLink',
            'target_record_key' => 'assigned_user_id',
            'target_module' => 'Employees',
            'width' => '15%',
        ],
        'edit_button' => [
            'vname' => 'LBL_EDIT_BUTTON',
            'widget_class' => 'SubPanelEditButton',
            'module' => 'Quotes',
            'width' => '4%',
        ],
        'currency_id' => [
            'usage' => 'query_only',
        ],
    ],
];
