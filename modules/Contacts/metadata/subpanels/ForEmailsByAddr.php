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
    'top_buttons' => [],
    'where' => '',
    'list_fields' => [
        'first_name' => [
            'name' => 'first_name',
            'usage' => 'query_only',
        ],
        'last_name' => [
            'name' => 'last_name',
            'usage' => 'query_only',
        ],
        'name' => [
            'name' => 'name',
            'vname' => 'LBL_LIST_NAME',
            'widget_class' => 'SubPanelDetailViewLink',
            'module' => 'Contacts',
            'width' => '23%',
        ],
        'account_name' => [
            'name' => 'account_name',
            'module' => 'Accounts',
            'target_record_key' => 'account_id',
            'target_module' => 'Accounts',
            'widget_class' => 'SubPanelDetailViewLink',
            'vname' => 'LBL_LIST_ACCOUNT_NAME',
            'width' => '22%',
            'sortable' => false,
        ],
        'account_id' => [
            'usage' => 'query_only',

        ],
        'email' => [
            'name' => 'email',
            'vname' => 'LBL_LIST_EMAIL',
            'widget_class' => 'SubPanelEmailLink',
            'width' => '30%',
            'sortable' => false,
        ],
        'edit_button' => [
            'vname' => 'LBL_EDIT_BUTTON',
            'widget_class' => 'SubPanelEditButton',
            'module' => 'Contacts',
            'width' => '5%',
        ],
    ],
];
