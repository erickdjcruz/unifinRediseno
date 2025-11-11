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
$module_name = '<module_name>';
$viewdefs[$module_name]['base']['view']['twitter'] = [
    'dashlets' => [
        [
            'name' => 'LBL_TWITTER_NAME',
            'description' => 'LBL_TWITTER_DESCRIPTION',
            'config' => [
                'limit' => '20',
            ],
            'preview' => [
                'title' => 'LBL_TWITTER_MY_ACCOUNT',
                'twitter' => 'sugarcrm',
                'limit' => '3',
            ],
        ],
    ],
    'config' => [
        'fields' => [
            [
                'name' => 'limit',
                'label' => 'LBL_TWITTER_DISPLAY_ROWS',
                'type' => 'enum',
                'options' => [
                    5 => 5,
                    10 => 10,
                    15 => 15,
                    20 => 20,
                    50 => 50,
                ],
            ],
        ],
    ],
];
