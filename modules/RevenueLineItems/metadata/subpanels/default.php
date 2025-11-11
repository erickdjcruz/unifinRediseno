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
            'width' => '10%',
            'default' => true,
        ],
        'sales_stage' => [
            'type' => 'enum',
            'vname' => 'LBL_SALES_STAGE',
            'width' => '10%',
            'default' => true,
        ],
        'probability' => [
            'type' => 'int',
            'vname' => 'LBL_PROBABILITY',
            'width' => '10%',
            'default' => true,
        ],
        'date_closed' => [
            'type' => 'date',
            'related_fields' => [
                0 => 'date_closed_timestamp',
            ],
            'vname' => 'LBL_DATE_CLOSED',
            'width' => '10%',
            'default' => true,
        ],
        'commit_stage' => [
            'type' => 'enum',
            'default' => true,
            'vname' => 'LBL_COMMIT_STAGE_FORECAST',
            'width' => '10%',
        ],
        'quantity' => [
            'vname' => 'LBL_QUANTITY',
            'width' => '10%',
            'default' => true,
        ],
        'discount_usdollar' => [
            'usage' => 'query_only',
        ],
        'remove_button' => [
            'vname' => 'LBL_REMOVE',
            'widget_class' => 'SubPanelRemoveButton',
            'width' => '4%',
        ],
    ],
];
