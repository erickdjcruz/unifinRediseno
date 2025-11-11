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

$viewdefs['Opportunities']['base']['view']['activity-card-content'] = [
    'panels' => [
        [
            'css_class' => 'panel-group flex',
            'fields' => [
                [
                    'name' => 'amount',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_LIKELY',
                            'css_class' => 'activity-label',
                        ],
                        'amount',
                    ],
                ],
                'sales_stage',
            ],
        ],
        [
            'css_class' => 'panel-group flex',
            'fields' => [
                [
                    'name' => 'service_start_date',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_SERVICE_START_DATE',
                            'css_class' => 'activity-label',
                        ],
                        'service_start_date',
                    ],
                ],
                [
                    'name' => 'service_duration',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_SERVICE_DURATION',
                            'css_class' => 'activity-label',
                        ],
                        'service_duration_value',
                        'service_duration_unit',
                        'service_duration',
                    ],
                ],
            ],
        ],
    ],
];
