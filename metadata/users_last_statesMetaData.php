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
$dictionary['users_last_states'] = [
    'table' => 'users_last_states',
    'fields' => [
        'id' => [
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'id',
            'required' => true,
        ],
        'user_id' => [
            'name' => 'user_id',
            'vname' => 'LBL_USER_ID',
            'type' => 'id',
        ],
        'last_state' => [
            'name' => 'last_state',
            'vname' => 'LBL_LAST_STATE',
            'type' => 'longtext',
        ],
        'platform' => [
            'name' => 'platform',
            'vname' => 'LBL_PLATFORM',
            'type' => 'varchar',
            'len' => 255,
        ],
        'deleted' => [
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'required' => false,
            'reportable' => false,
        ],
    ],
    'indices' => [
        [
            'name' => 'users_last_statespk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_users_last_states',
            'type' => 'index',
            'fields' => [
                'user_id',
                'platform',
            ],
        ],
    ],
];
