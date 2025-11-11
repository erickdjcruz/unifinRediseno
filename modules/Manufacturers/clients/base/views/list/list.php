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

$viewdefs['Manufacturers']['base']['view']['list'] = [
    'favorites' => false,
    'panels' => [
        [
            'name' => 'panel_header',
            'fields' => [
                [
                    'name' => 'name',
                    'link' => true,
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'status',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'list_order',
                    'enabled' => true,
                    'default' => true,
                ],
            ],
        ],
    ],
];
