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
        'account_name' => [
            'type' => 'enum',
            'vname' => 'LBL_ACCOUNT_NAME',
            'width' => '10%',
            'default' => true,
        ],
        'product_template_name' => [
            'type' => 'int',
            'vname' => 'LBL_PRODUCT_TEMPLATE_NAME',
            'width' => '10%',
            'default' => true,
        ],
        'start_date' => [
            'type' => 'date',
            'vname' => 'LBL_START_DATE',
            'width' => '10%',
            'default' => true,
        ],
        'end_date' => [
            'type' => 'date',
            'vname' => 'LBL_END_DATE',
            'width' => '10%',
            'default' => true,
        ],
        'service' => [
            'type' => 'bool',
            'default' => true,
            'vname' => 'LBL_SERVICE',
            'width' => '10%',
        ],
    ],
];
