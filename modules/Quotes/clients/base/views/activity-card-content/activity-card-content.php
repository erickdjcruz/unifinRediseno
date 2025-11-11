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

$viewdefs['Quotes']['base']['view']['activity-card-content'] = [
    'sort_by' => 'date_entered',
    'sort_order' => 'asc',
    'panels' => [
        [
            'css_class' => 'panel-group flex',
            'fields' => [
                [
                    'name' => 'date_quote_expected_closed',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_DATE_QUOTE_EXPECTED_CLOSED',
                            'css_class' => 'activity-label',
                        ],
                        'date_quote_expected_closed',
                    ],
                ],
            ],
        ],
        [
            'css_class' => 'panel-group flex',
            'fields' => [
                [
                    'name' => 'total',
                    'type' => 'fieldset',
                    'css_class' => 'flex',
                    'fields' => [
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_GRAND_TOTAL',
                            'css_class' => 'activity-label',
                        ],
                        'total',
                    ],
                ],
            ],
        ],
    ],
];
