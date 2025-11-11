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
    ],

    'where' => '',


    'list_fields' => [
        'tracker_name' => [
            'vname' => 'LBL_SUBPANEL_TRACKER_NAME',
            'widget_class' => 'SubPanelDetailViewLink',
            'width' => '20%',
        ],
        'tracker_url' => [
            'vname' => 'LBL_SUBPANEL_TRACKER_URL',
            'width' => '60%',
        ],
        'tracker_key' => [
            'vname' => 'LBL_SUBPANEL_TRACKER_KEY',
            'width' => '10%',
        ],
        'edit_button' => [
            'vname' => 'LBL_EDIT_BUTTON',
            'widget_class' => 'SubPanelEditButton',
            'module' => 'Cases',
            'width' => '5%',
        ],
        'remove_button' => [
            'vname' => 'LBL_REMOVE',
            'widget_class' => 'SubPanelRemoveButton',
            'module' => 'Cases',
            'width' => '5%',
        ],
    ],
];
