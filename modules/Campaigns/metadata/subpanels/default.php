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
        ['widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'Campaigns'],
    ],

    'where' => '',

    'list_fields' => [
        'name' => [
            'name' => 'name',
            'vname' => 'LBL_LIST_CAMPAIGN_NAME',
            'widget_class' => 'SubPanelDetailViewLink',
            'width' => '85%',
        ],
        'status' => [
            'name' => 'status',
            'vname' => 'LBL_LIST_STATUS',
            'width' => '15%',
        ],
        'edit_button' => [
            'vname' => 'LBL_EDIT_BUTTON',
            'widget_class' => 'SubPanelEditButton',
            'module' => 'Campaigns',
            'width' => '5%',
        ],
        'remove_button' => [
            'vname' => 'LBL_REMOVE',
            'widget_class' => 'SubPanelRemoveButton',
            'module' => 'Campgains',
            'width' => '5%',
        ],
    ],
];
