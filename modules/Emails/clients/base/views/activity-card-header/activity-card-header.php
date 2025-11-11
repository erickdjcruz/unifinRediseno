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

$viewdefs['Emails']['base']['view']['activity-card-header'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_HEADER',
            'css_class' => 'panel-header',
            'fields' => [
                [
                    'name' => 'name',
                    'type' => 'relate',
                    'link' => true,
                    'id_name' => 'id',
                ],
                [
                    'name' => 'state',
                    'css_class' => 'label-state',
                ],
            ],
        ],
        [
            'name' => 'panel_users',
            'label' => 'LBL_PANEL_USERS',
            'css_class' => 'panel-users mt-2 flex flex-wrap gap-x-4 gap-y-2',
            'template' => 'user-group',
            'fields' => [
                [
                    'name' => 'to_collection',
                    'type' => 'email-recipients',
                ],
                [
                    'name' => 'from_collection',
                    'type' => 'from',
                ],
                [
                    'name' => 'cc_collection',
                    'type' => 'email-recipients',
                ],
                [
                    'name' => 'bcc_collection',
                    'type' => 'email-recipients',
                ],
            ],
        ],
    ],
];
