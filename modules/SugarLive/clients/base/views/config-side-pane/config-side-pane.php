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
$viewdefs['SugarLive']['base']['view']['config-side-pane'] = [
    'label' => 'LBL_MODULE_NAME',
    'leftpanel' => [
        'label' => 'LBL_SUGARLIVE_LAYOUT',
        'fields' => [
            [
                'name' => 'columns',
                'vname' => 'LBL_SUGARLIVE_LAYOUT',
                'type' => 'selected-field-list',
                'css_class' => 'columns',
            ],
        ],
    ],
    'rightpanel' => [
        'label' => 'LBL_SUGARLIVE_AVAILABLE_FIELDS',
        'fields' => [
            [
                'name' => 'available-fields-list',
                'vname' => 'LBL_SUGARLIVE_AVAILABLE_FIELDS',
                'type' => 'available-field-list',
                'css_class' => 'fields',
            ],
        ],
    ],
];
