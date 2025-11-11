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
        'holiday_date' => [
            'vname' => 'LBL_HOLIDAY_DATE',
            'widget_class' => 'SubPanelDetailViewLink',
            'width' => '21%',
        ],
        'description' => [
            'vname' => 'LBL_DESCRIPTION',
            'width' => '75%',
            'sortable' => false,
        ],
        'edit_button' => [
            'widget_class' => 'SubPanelEditButton',
            'width' => '2%',
        ],


    ],
];
