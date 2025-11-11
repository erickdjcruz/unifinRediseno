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

$viewdefs['PurchasedLineItems']['base']['view']['activity-card-content'] = [
    'panels' => [
        [
            'css_class' => 'panel-group flex',
            'fields' => [
                [
                    'name' => 'purchase_name',
                    'show_avatar' => true,
                ],
            ],
        ],
        [
            'css_class' => 'panel-group flex',
            'fields' => [
                [
                    'name' => 'product_template_name',
                    'show_avatar' => true,
                ],
                [
                    'name' => 'quantity',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_QUANTITY',
                            'css_class' => 'activity-label',
                        ],
                        'quantity',
                    ],
                ],
            ],
        ],
        [
            'css_class' => 'panel-group flex',
            'fields' => [
                [
                    'name' => 'revenue',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_REVENUE',
                            'css_class' => 'activity-label',
                        ],
                        'revenue',
                    ],
                ],
                [
                    'name' => 'date_closed',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_DATE_CLOSED',
                            'css_class' => 'activity-label',
                        ],
                        'date_closed',
                    ],
                ],
            ],
        ],
    ],
];
