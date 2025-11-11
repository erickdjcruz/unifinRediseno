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

$viewdefs['ExternalUsers']['base']['view']['record'] = [
    'buttons' => [
        [
            'name' => 'sidebar_toggle',
            'type' => 'sidebartoggle',
        ],
    ],
    'panels' => [
        [
            'name' => 'panel_header',
            'header' => true,
            'fields' => [
                [
                    'name' => 'name',
                    'type' => 'fullname',
                    'label' => 'LBL_NAME',
                    'dismiss_label' => true,
                    'readonly' => true,
                    'fields' => [
                        [
                            'name' => 'salutation',
                            'type' => 'enum',
                            'enum_width' => 'auto',
                            'searchBarThreshold' => 7,
                        ],
                        'first_name',
                        'last_name',
                    ],
                ],
            ],
        ],
        [
            'name' => 'panel_body',
            'label' => 'LBL_RECORD_BODY',
            'columns' => 2,
            'labels' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'user_name',
                    'readonly' => true,
                ],
                [
                    'name' => 'status',
                    'readonly' => true,
                ],
                [
                    'name' => 'external_app',
                    'readonly' => true,
                ],
                [
                    'name' => 'external_id',
                    'readonly' => true,
                ],
                [
                    'name' => 'parent_name',
                    'readonly' => true,
                ],
            ],
        ],
        [
            'name' => 'panel_hidden',
            'label' => 'LBL_RECORD_SHOWMORE',
            'hide' => true,
            'columns' => 2,
            'labels' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'date_entered_by',
                    'readonly' => true,
                    'inline' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_ENTERED',
                    'fields' => [
                        [
                            'name' => 'date_entered',
                        ],
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_BY',
                        ],
                        [
                            'name' => 'created_by_name',
                        ],
                    ],
                ],
                [
                    'name' => 'date_modified_by',
                    'readonly' => true,
                    'inline' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_MODIFIED',
                    'fields' => [
                        [
                            'name' => 'date_modified',
                        ],
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_BY',
                        ],
                        [
                            'name' => 'modified_by_name',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
