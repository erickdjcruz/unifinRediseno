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
        ['widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'Contracts', 'mode' => 'MultiSelect'],
    ],

    'where' => '',


    'list_fields' => [
        'name' => [
            'name' => 'name',
            'vname' => 'LBL_LIST_NAME',
            'widget_class' => 'SubPanelDetailViewLink',
            'module' => 'Contacts',
            'width' => '33%',
        ],
        'start_date' => [
            'name' => 'start_date',
            'vname' => 'LBL_LIST_START_DATE',
            'width' => '10%',
        ],
        'end_date' => [
            'name' => 'end_date',
            'vname' => 'LBL_LIST_END_DATE',
            'width' => '10%',
        ],
        'status' => [
            'name' => 'status',
            'vname' => 'LBL_LIST_STATUS',
            'width' => '10%',
        ],
        'total_contract_value' => [
            'name' => 'total_contract_value',
            'vname' => 'LBL_LIST_CONTRACT_VALUE',
            'width' => '15%',
        ],
        'edit_button' => [
            'vname' => 'LBL_EDIT_BUTTON',
            'widget_class' => 'SubPanelEditButton',
            'module' => 'Contracts',
            'width' => '5%',
        ],
        'remove_button' => [
            'vname' => 'LBL_REMOVE',
            'widget_class' => 'SubPanelRemoveButton',
            'module' => 'Contracts',
            'width' => '5%',
        ],
    ],
];
