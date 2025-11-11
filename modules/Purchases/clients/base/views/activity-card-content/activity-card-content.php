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

$viewdefs['Purchases']['base']['view']['activity-card-content'] = [
    'panels' => [
        [
            'css_class' => 'panel-group flex',
            'fields' => [
                [
                    'name' => 'product_template_name',
                    'show_avatar' => true,
                ],
                [
                    'name' => 'total_quantity',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_TOTAL_QUANTITY',
                            'css_class' => 'activity-label',
                        ],
                        'total_quantity',
                    ],
                ],
            ],
        ],
        [
            'css_class' => 'panel-group flex',
            'fields' => [
                [
                    'name' => 'total_revenue',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_TOTAL_REVENUE',
                            'css_class' => 'activity-label',
                        ],
                        'total_revenue',
                    ],
                ],
            ],
        ],
        [
            'css_class' => 'panel-group flex',
            'fields' => [
                [
                    'name' => 'start_date',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_START_DATE',
                            'css_class' => 'activity-label',
                        ],
                        'start_date',
                    ],
                ],
                [
                    'name' => 'end_date',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_END_DATE',
                            'css_class' => 'activity-label',
                        ],
                        'end_date',
                    ],
                ],
            ],
        ],
    ],
];
