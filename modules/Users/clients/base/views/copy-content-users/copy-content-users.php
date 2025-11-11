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
$viewdefs['Users']['base']['view']['copy-content-users'] = [
    'fields' => [
        'users_select' => [
            'name' => 'users_select',
            'type' => 'hybrid-select',
            'select_module' => 'Users',
            'view_template' => 'edit',
            'placeholder' => 'LBL_SELECT_DESTINATION_USERS',
        ],
        'teams_select' => [
            'name' => 'teams_select',
            'type' => 'hybrid-select',
            'select_module' => 'Teams',
            'view_template' => 'edit',
            'placeholder' => 'LBL_SELECT_DESTINATION_TEAMS',
        ],
        'roles_select' => [
            'name' => 'roles_select',
            'type' => 'hybrid-select',
            'select_module' => 'ACLRoles',
            'view_template' => 'edit',
            'placeholder' => 'LBL_SELECT_DESTINATION_ROLES',
        ],
    ],
];
