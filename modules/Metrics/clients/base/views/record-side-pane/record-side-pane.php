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
$viewdefs['Metrics']['base']['view']['record-side-pane'] = [
    'label' => 'LBL_MODULE_NAME',
    'left-panels' => [
        [
            'label' => 'LBL_METRIC_COLUMNS',
            'fields' => [
                [
                    'name' => 'columns',
                    'vname' => 'LBL_METRIC_COLUMNS',
                    'type' => 'field-list',
                    'css_class' => 'columns',
                ],
            ],
        ],
        [
            'fields' => [
                [
                    'name' => 'multi_field_column',
                    'type' => 'multi-field-label',
                ],
            ],
        ],
    ],
    'right-panels' => [
        [
            'label' => 'LBL_METRIC_AVAILABLE_FIELDS',
            'fields' => [
                [
                    'name' => 'available-fields',
                    'vname' => 'LBL_METRIC_AVAILABLE_FIELDS',
                    'type' => 'available-field-list',
                    'css_class' => 'fields',
                ],
            ],
        ],
    ],
];
