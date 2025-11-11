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

$viewdefs['PubSub_ModuleEvent_PushSubs']['base']['view']['list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'target_module',
                    'default' => true,
                    'enabled' => true,
                    'sortable' => true,
                ],
                [
                    'name' => 'webhook_url',
                    'default' => true,
                    'enabled' => true,
                    'sortable' => true,
                ],
                [
                    'name' => 'expiration_date',
                    'default' => true,
                    'enabled' => true,
                    'sortable' => true,
                ],
                [
                    'name' => 'date_entered',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'created_by_name',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'date_modified',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'modified_by_name',
                    'default' => true,
                    'enabled' => true,
                ],
            ],
        ],
    ],
];
