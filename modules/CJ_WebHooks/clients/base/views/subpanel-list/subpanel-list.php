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
$viewdefs['CJ_WebHooks']['base']['view']['subpanel-list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'sort_order',
                    'label' => 'LBL_SORT_ORDER',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'name',
                    'label' => 'LBL_NAME',
                    'enabled' => true,
                    'default' => true,
                    'link' => true,
                ],
                [
                    'name' => 'active',
                    'label' => 'LBL_ACTIVE',
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
            ],
        ],
    ],
    'orderBy' => [
        'field' => 'sort_order',
        'direction' => 'asc',
    ],
];
