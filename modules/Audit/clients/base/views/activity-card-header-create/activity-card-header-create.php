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

$viewdefs['Audit']['base']['view']['activity-card-header-create'] = [
    'panels' => [
        [
            'name' => 'panel_users',
            'css_class' => 'panel-users',
            'template' => 'user-single',
            'fields' => [
                [
                    'label' => 'LBL_CREATED',
                    'name' => 'created_by_name',
                    'type' => 'relate',
                ],
            ],
        ],
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_HEADER',
            'css_class' => 'panel-header panel-header-create',
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
