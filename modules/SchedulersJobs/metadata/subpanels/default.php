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
        /*array('widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'Queues'),*/
    ],
    'where' => '',

    'fill_in_additional_fields' => true,
    'list_fields' => [
        'name' => [
            'vname' => 'LBL_NAME',
            'width' => '20%',
            'sortable' => false,
        ],
        'status' => [
            'vname' => 'LBL_STATUS',
            'width' => '10%',
            'sortable' => true,
        ],
        'resolution' => [
            'vname' => 'LBL_RESOLUTION',
            'width' => '10%',
            'sortable' => true,
        ],
        'message' => [
            'vname' => 'LBL_MESSAGE',
            'width' => '30%',
            'sortable' => false,
        ],
        'execute_time' => [
            'vname' => 'LBL_EXECUTE_TIME',
            'width' => '10%',
            'sortable' => true,
        ],
        'date_modified' => [
            'vname' => 'LBL_DATE_MODIFIED',
            'width' => '10%',
            'sortable' => true,
        ],
    ],
];
