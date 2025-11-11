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

$viewdefs['base']['view']['active-subscriptions'] = [
    'dashlets' => [
        [
            'label' => 'LBL_ACTIVE_SUBSCRIPTIONS_DASHLET',
            'description' => 'LBL_ACTIVE_SUBSCRIPTIONS_DASHLET_DESCRIPTION',
            'config' => [
                'linked_subscriptions_account_field' => null,
            ],
            'preview' => [
                'linked_subscriptions_account_field' => null,
            ],
            'filter' => [
                'view' => 'record',
            ],
        ],
    ],
    'panels' => [
        [
            'name' => 'panel_body',
            'columns' => 1,
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'linked_subscriptions_account_field',
                    'label' => 'LBL_LINKED_SUBSCRIPTIONS_ACCOUNT_FIELD',
                    'options' => '',
                    'type' => 'enum',
                    'span' => 6,
                    'required' => true,
                ],
            ],
        ],
    ],
    'fields' => [
        'name',
        'start_date',
        'end_date',
        'pli_collection',
    ],
];
