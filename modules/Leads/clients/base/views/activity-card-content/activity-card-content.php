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

$viewdefs['Leads']['base']['view']['activity-card-content'] = [
    'panels' => [
        [
            'css_class' => 'panel-group flex',
            'fields' => [
                'account_name',
                'title',
            ],
        ],
        [
            'css_class' => 'panel-group flex',
            'fields' => [
                'lead_source',
                'status',
                [
                    'name' => 'do_not_call',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_DO_NOT_CALL',
                            'css_class' => 'activity-label',
                        ],
                        'do_not_call',
                    ],
                ],
            ],
        ],
        [
            'css_class' => 'panel-group flex',
            'fields' => [
                'phone_work',
                'email',
            ],
        ],
        [
            'css_class' => 'panel-body',
            'fields' => [
                [
                    'name' => 'tag',
                ],
            ],
        ],
    ],
];
