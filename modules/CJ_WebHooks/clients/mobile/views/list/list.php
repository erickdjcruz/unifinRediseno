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
$viewdefs['CJ_WebHooks']['mobile']['view']['list'] = [
    'panels' => [
        [
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => [
                [
                    'name' => 'name',
                    'label' => 'LBL_NAME',
                    'enabled' => true,
                    'default' => true,
                    'link' => true,
                ],
                [
                    'name' => 'parent_name',
                    'label' => 'LBL_PARENT_NAME',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'trigger_event',
                    'label' => 'LBL_TRIGGER_EVENT',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'request_method',
                    'label' => 'LBL_REQUEST_METHOD',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'url',
                    'label' => 'LBL_URL',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'date_modified',
                    'label' => 'LBL_DATE_MODIFIED',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'team_name',
                    'label' => 'LBL_TEAM',
                    'width' => 9,
                    'default' => true,
                    'enabled' => true,
                ],
            ],
        ],
    ],
];
