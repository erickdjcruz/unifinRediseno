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
        ['widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'Bugs'],
    ],

    'where' => '',


    'list_fields' => [
        'bug_number' => [
            'vname' => 'LBL_LIST_NUMBER',
            'width' => '5%',
        ],

        'name' => [
            'vname' => 'LBL_LIST_SUBJECT',
            'widget_class' => 'SubPanelDetailViewLink',
            'width' => '50%',
        ],
        'status' => [
            'vname' => 'LBL_LIST_STATUS',
            'width' => '15%',
        ],
        'type' => [
            'vname' => 'LBL_LIST_TYPE',
            'width' => '15%',
        ],
        'priority' => [
            'vname' => 'LBL_LIST_PRIORITY',
            'width' => '11%',
        ],
        'edit_button' => [
            'widget_class' => 'SubPanelEditButton',
            'module' => 'Bugs',
            'width' => '4%',
        ],
        'remove_button' => [
            'widget_class' => 'SubPanelRemoveButton',
            'module' => 'Bugs',
            'width' => '5%',
        ],
    ],
];
