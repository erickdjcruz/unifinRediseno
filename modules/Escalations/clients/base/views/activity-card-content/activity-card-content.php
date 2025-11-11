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

$viewdefs['Escalations']['base']['view']['activity-card-content'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'css_class' => 'panel-group flex',
            'fields' => [
                'status',
                'source',
                [
                    'name' => 'reason',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_REASON',
                            'css_class' => 'activity-label',
                        ],
                        'reason',
                    ],
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
