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
$viewdefs['Quotes']['base']['view']['quote-data-grand-totals-header'] = [
    'buttons' => [
        [
            'type' => 'quote-data-actiondropdown',
            'name' => 'panel_dropdown',
            'no_default_action' => true,
            'buttons' => [
                [
                    'type' => 'button',
                    'icon' => 'sicon-plus',
                    'name' => 'create_qli_button',
                    'label' => 'LBL_CREATE_QLI_BUTTON_LABEL',
                    'acl_action' => 'create',
                    'tooltip' => 'LBL_CREATE_QLI_BUTTON_TOOLTIP',
                ],
                [
                    'type' => 'button',
                    'icon' => 'sicon-plus',
                    'name' => 'create_comment_button',
                    'label' => 'LBL_CREATE_COMMENT_BUTTON_LABEL',
                    'acl_action' => 'create',
                    'tooltip' => 'LBL_CREATE_COMMENT_BUTTON_TOOLTIP',
                ],
                [
                    'type' => 'button',
                    'icon' => 'sicon-plus',
                    'name' => 'create_group_button',
                    'label' => 'LBL_CREATE_GROUP_BUTTON_LABEL',
                    'acl_action' => 'create',
                    'tooltip' => 'LBL_CREATE_GROUP_BUTTON_TOOLTIP',
                ],
            ],
        ],
    ],
    'panels' => [
        [
            'name' => 'panel_quote_data_grand_totals_header',
            'label' => 'LBL_QUOTE_DATA_GRAND_TOTALS_HEADER',
            'fields' => [
                [
                    'name' => 'deal_tot',
                    'label' => 'LBL_LIST_DEAL_TOT',
                    'css_class' => 'quote-totals-row-item',
                    'related_fields' => ['deal_tot_discount_percentage'],
                ],
                [
                    'name' => 'new_sub',
                    'css_class' => 'quote-totals-row-item',
                ],
                [
                    'name' => 'tax',
                    'label' => 'LBL_TAX_TOTAL',
                    'css_class' => 'quote-totals-row-item',
                ],
                [
                    'name' => 'shipping',
                    'css_class' => 'quote-totals-row-item',
                ],
                [
                    'name' => 'total',
                    'label' => 'LBL_LIST_GRAND_TOTAL',
                    'css_class' => 'quote-totals-row-item',
                ],
            ],
        ],
    ],
];
