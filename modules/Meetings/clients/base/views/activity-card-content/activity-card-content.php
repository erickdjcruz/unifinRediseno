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

$viewdefs['Meetings']['base']['view']['activity-card-content'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
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
                [
                    'name' => 'status',
                    'type' => 'event-status',
                ],
            ],
        ],
        [
            'name' => 'panel_body',
            'label' => 'LBL_PANEL_2',
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
