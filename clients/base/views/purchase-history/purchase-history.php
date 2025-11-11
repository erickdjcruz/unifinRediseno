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

$viewdefs['base']['view']['purchase-history'] = [
    'template' => 'list',
    'dashlets' => [
        [
            'label' => 'LBL_PURCHASE_HISTORY_DASHLET',
            'description' => 'LBL_PURCHASE_HISTORY_DASHLET_DESCRIPTION',
            'config' => [
                'linked_account_field' => null,
                'limit' => null,
            ],
            'preview' => [
                'linked_account_field' => null,
                'limit' => null,
            ],
            'filter' => [
                'view' => 'record',
            ],
        ],
    ],
    'panels' => [
        [
            'name' => 'panel_body',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'linked_account_field',
                    'label' => 'LBL_LINKED_SUBSCRIPTIONS_ACCOUNT_FIELD',
                    'options' => '',
                    'type' => 'enum',
                    'span' => 6,
                    'required' => true,
                ],
                [
                    'name' => 'limit',
                    'label' => 'LBL_DASHLET_CONFIGURE_DISPLAY_ROWS',
                    'type' => 'enum',
                    'options' => 'dashlet_limit_options',
                    'required' => true,
                ],
            ],
        ],
    ],
    'fields' => [
        'name',
        'start_date',
        'end_date',
        'total_quantity',
        'total_revenue',
        'pli_count',
    ],
];
