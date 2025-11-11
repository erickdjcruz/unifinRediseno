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

$viewdefs['RevenueLineItems']['base']['view']['activity-card-content'] = [
    'sort_by' => 'date_closed',
    'sort_order' => 'asc',
    'panels' => [
        [
            'css_class' => 'panel-group flex',
            'fields' => [
                [
                    'name' => 'opportunity_name',
                    'show_avatar' => true,
                ],
            ],
        ],
        [
            'css_class' => 'panel-group flex',
            'fields' => [
                [
                    'name' => 'sales_stage',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_SALES_STAGE',
                            'css_class' => 'activity-label',
                        ],
                        'sales_stage',
                    ],
                ],
                [
                    'name' => 'commit_stage',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_COMMIT_STAGE_FORECAST',
                            'css_class' => 'activity-label',
                        ],
                        'commit_stage',
                    ],
                ],
                [
                    'name' => 'probability',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_PROBABILITY',
                            'css_class' => 'activity-label',
                        ],
                        'probability',
                    ],
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
                [
                    'name' => 'category_name',
                    'type' => 'relate',
                    'link' => true,
                ],
            ],
        ],
        [
            'css_class' => 'panel-group flex',
            'fields' => [
                [
                    'name' => 'discount_price',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_DISCOUNT_PRICE',
                            'css_class' => 'activity-label',
                        ],
                        'discount_price',
                    ],
                ],
                [
                    'name' => 'likely_case',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_LIKELY',
                            'css_class' => 'activity-label',
                        ],
                        'likely_case',
                    ],
                ],
            ],
        ],
        [
            'css_class' => 'panel-group flex',
            'fields' => [
                [
                    'name' => 'service',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_SERVICE',
                            'css_class' => 'activity-label',
                        ],
                        'service',
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
