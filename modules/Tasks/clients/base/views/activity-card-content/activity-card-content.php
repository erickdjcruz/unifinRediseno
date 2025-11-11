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

$viewdefs['Tasks']['base']['view']['activity-card-content'] = [
    'sort_by' => 'date_due',
    'sort_order' => 'asc',
    'panels' => [
        [
            'name' => 'panel_status',
            'css_class' => 'panel-status flex',
            'columns' => 2,
            'fields' => [
                [
                    'name' => 'status',
                ],
                [
                    'name' => 'priority',
                    'css_class' => 'ml-2',
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
