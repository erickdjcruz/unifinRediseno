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

$viewdefs['Calls']['base']['view']['activity-card-content'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'css_class' => 'panel-header',
            'fields' => [
                [
                    'name' => 'duration',
                    'type' => 'duration',
                    'fields' => [
                        'date_start',
                        'date_end',
                    ],
                    'related_fields' => [
                        'duration_hours',
                        'duration_minutes',
                    ],
                ],
            ],
        ],
        [
            'name' => 'panel_sentiment',
            'css_class' => 'panel-body',
            'fields' => [
                [
                    'name' => 'sentiment_score_customer',
                    'type' => 'sentiment',
                ],
            ],
        ],
        [
            'name' => 'panel_body',
            'css_class' => 'panel-body',
            'fields' => [
                [
                    'name' => 'description',
                    'settings' => [
                        'max_display_chars' => 10000,
                        'collapsed' => false,
                    ],
                ],
            ],
        ],
    ],
];
