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
$viewdefs['Metrics']['base']['view']['config-tab-settings'] = [
    'label' => 'LBL_MODULE_NAME',
    'left-panels' => [
        [
            'label' => 'LBL_VISIBLE_METRIC_TABS',
            'fields' => [
                [
                    'name' => 'visible-fields',
                    'vname' => 'LBL_CONSOLE_COLUMNS',
                    'type' => 'visible-field-list',
                    'css_class' => 'columns',
                ],
            ],
        ],
    ],
    'right-panels' => [
        [
            'label' => 'LBL_HIDDEN_METRIC_TABS',
            'fields' => [
                [
                    'name' => 'hidden-fields',
                    'vname' => 'LBL_CONSOLE_AVAILABLE_FIELDS',
                    'type' => 'hidden-field-list',
                    'css_class' => 'fields',
                ],
            ],
        ],
    ],
];
