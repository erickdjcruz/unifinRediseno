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

$viewdefs['Audit']['base']['view']['activity-card-header'] = [
    'panels' => [
        [
            'name' => 'panel_users',
            'css_class' => 'panel-users mt-2 flex flex-wrap gap-x-4 gap-y-2',
            'template' => 'user-single',
            'fields' => [
                [
                    'type' => 'relate',
                    'name' => 'created_by_name',
                    'id_name' => 'created_by',
                    'link' => true,
                    'module' => 'Employees',
                ],
            ],
        ],
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_header',
            'css_class' => 'panel-header',
            'fields' => [],
        ],
    ],
];
