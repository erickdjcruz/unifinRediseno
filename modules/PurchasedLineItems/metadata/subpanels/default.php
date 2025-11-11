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
        ['widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'PurchasedLineItems'],
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
        'purchase_name' => [
            'type' => 'enum',
            'vname' => 'LBL_PURCHASE',
            'width' => '10%',
            'default' => true,
        ],
        'product_template_name' => [
            'type' => 'enum',
            'vname' => 'LBL_PRODUCT_TEMPLATE',
            'width' => '10%',
            'default' => true,
        ],
        'revenue' => [
            'type' => 'currency',
            'vname' => 'LBL_REVENUE',
            'width' => '10%',
            'default' => true,
        ],
        'total_amount' => [
            'type' => 'currency',
            'vname' => 'LBL_CALCULATED_LINE_ITEM_AMOUNT',
            'width' => '10%',
            'default' => true,
        ],
        'quantity' => [
            'type' => 'decimal',
            'default' => true,
            'vname' => 'LBL_QUANTITY',
            'width' => '10%',
        ],
    ],
];
