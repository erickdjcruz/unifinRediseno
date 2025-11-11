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

$viewdefs['Contacts']['base']['view']['activity-card-content'] = [
    'panels' => [
        [
            'css_class' => 'panel-group flex',
            'fields' => [
                [
                    'name' => 'account_name',
                    'show_avatar' => true,
                ],
                'title',
            ],
        ],
        [
            'css_class' => 'panel-group flex',
            'fields' => [
                'primary_address_city',
                'primary_address_state',
                'primary_address_country',
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
                'phone_mobile',
                'email',
            ],
        ],
    ],
];
