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

$viewdefs['Calls']['base']['view']['activity-card-header'] = [
    'panels' => [
        [
            'name' => 'panel_users',
            'label' => 'LBL_INVITEES',
            'css_class' => 'panel-users mt-2 flex flex-wrap gap-y-2',
            'template' => 'user-list',
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
        [
            'name' => 'panel_header',
            'css_class' => 'panel-header',
            'fields' => [
                [
                    'name' => 'name',
                    'type' => 'relate',
                    'link' => true,
                    'id_name' => 'id',
                ],
            ],
        ],
    ],
];
