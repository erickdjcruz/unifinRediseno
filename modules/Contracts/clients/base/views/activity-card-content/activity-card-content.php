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

$viewdefs['Contracts']['base']['view']['activity-card-content'] = [
    'panels' => [
        [
            'css_class' => 'panel-body',
            'fields' => [
                [
                    'name' => 'account_name',
                    'show_avatar' => true,
                ],
            ],
        ],
        [
            'css_class' => 'panel-group flex',
            'fields' => [
                'status',
                [
                    'name' => 'start_date',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_START',
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
                            'default_value' => 'LBL_END',
                            'css_class' => 'activity-label',
                        ],
                        'end_date',
                    ],
                ],
            ],
        ],
    ],
];
