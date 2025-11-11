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

$viewdefs['Messages']['base']['view']['activity-card-content'] = [
    'panels' => [
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
        [
            'name' => 'panel_status',
            'css_class' => 'panel-status',
            'fields' => [
                'date_start',
                'date_end',
            ],
        ],
        [
            'name' => 'panel_conversation',
            'css_class' => 'panel-conversation',
            'fields' => [
                [
                    'name' => 'conversation',
                    'type' => 'conversation',
                    'settings' => [
                        'max_display_messages' => 100,
                    ],
                ],
            ],
        ],
    ],
];
