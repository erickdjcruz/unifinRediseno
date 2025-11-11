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

$viewdefs['Home']['base']['view']['webpage'] = [
    'dashlets' => [
        [
            'label' => 'LBL_DASHLET_WEBPAGE_NAME',
            'description' => 'LBL_DASHLET_WEBPAGE_DESC',
            'config' => [
                'url' => '',
                'module' => 'Home',
                'limit' => 3,
            ],
            'preview' => [
                'title' => 'LBL_DASHLET_WEBPAGE_NAME',
                'url' => '',
                'limit' => '3',
                'module' => 'Home',
            ],
            'filter' => [
                'blacklist' => [
                    'module' => 'Administration',
                ],
            ],
        ],
    ],
    'config' => [
        'fields' => [
            [
                'type' => 'iframe',
                'name' => 'url',
                'label' => 'LBL_DASHLET_WEBPAGE_URL',
                'help' => 'LBL_DASHLET_WEBPAGE_URL_HELP',
            ],
            [
                'name' => 'limit',
                'label' => 'LBL_DASHLET_CONFIGURE_DISPLAY_ROWS',
                'type' => 'enum',
                'options' => 'dashlet_webpage_limit_options',
            ],
        ],
    ],
    'view_panel' => [
        [
            'type' => 'iframe',
            'name' => 'url',
            'label' => 'LBL_DASHLET_WEBPAGE_URL',
            'width' => '100%',
        ],
    ],
];
