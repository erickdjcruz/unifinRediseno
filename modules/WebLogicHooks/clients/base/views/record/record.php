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

$viewdefs['WebLogicHooks']['base']['view']['record'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'header' => true,
            'fields' => [
                [
                    'name' => 'name',
                    'required' => true,
                    'label' => 'LBL_NAME',
                ],
                [
                    'type' => 'follow',
                    'readonly' => true,
                ],
            ],
        ],
        [
            'name' => 'panel_body',
            'label' => 'LBL_RECORD_BODY',
            'columns' => 2,
            'placeholders' => true,
            'fields' => [
                'url',
                'webhook_target_module',
                'trigger_event',
                'request_method',
            ],
        ],
    ],
    'dependencies' => [
        [
            'hooks' => ['all'],
            'trigger' => 'true',
            'triggerFields' => ['trigger_event'],
            'onload' => true,
            'actions' => [
                [
                    'action' => 'SetVisibility',
                    'params' => [
                        'target' => 'webhook_target_module',
                        'value' => 'not(isInList($trigger_event, createList("after_login", "after_logout", "login_failed")))',
                    ],
                ],
                [
                    'action' => 'SetValue',
                    'params' => [
                        'target' => 'webhook_target_module',
                        'value' => 'ifElse(isInList($trigger_event, createList("after_login", "after_logout", "login_failed")), "Users", $webhook_target_module)',
                    ],
                ],
            ],
        ],
    ],
];
