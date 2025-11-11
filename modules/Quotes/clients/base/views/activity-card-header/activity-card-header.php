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

$viewdefs['Quotes']['base']['view']['activity-card-header'] = [
    'panels' => [
        [
            'name' => 'panel_users',
            'label' => 'LBL_ASSIGNED_TO',
            'css_class' => 'panel-users mt-2 flex flex-wrap gap-x-4 gap-y-2flex',
            'template' => 'user-single',
            'fields' => [
                [
                    'name' => 'assigned_user_name',
                    'type' => 'relate',
                ],
            ],
        ],
        [
            'name' => 'panel_users_before',
            'css_class' => 'panel-group flex',
            'fields' => [
                [
                    'name' => 'billing_account_name',
                    'type' => 'relate',
                    'show_avatar' => true,
                ],
                'quote_stage',
            ],
        ],
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_HEADER',
            'css_class' => 'panel-header',
            'fields' => [
                [
                    'label' => 'LBL_NAME',
                    'name' => 'name',
                    'type' => 'relate',
                    'link' => true,
                    'id_name' => 'id',
                ],
            ],
        ],
    ],
];
