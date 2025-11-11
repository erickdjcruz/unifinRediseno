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

$viewdefs['Audit']['base']['view']['activity-card-content'] = [
    'panels' => [
        [
            'name' => 'panel_change',
            'label' => 'LBL_PANEL_1',
            'css_class' => 'panel-change mt-2',
            'fields' => [
                [
                    'name' => 'field_name',
                    'type' => 'fieldtype',
                    'css_class' => 'change-field-name',
                ],
                [
                    'name' => 'before',
                    'type' => 'base',
                    'css_class' => 'change-before-value',
                ],
                [
                    'name' => 'after',
                    'type' => 'base',
                    'css_class' => 'change-after-value',
                ],
            ],
        ],
    ],
];
