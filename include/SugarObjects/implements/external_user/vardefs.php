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

/**
 *
 * External user
 * @var array
 */
$vardefs = [
    'fields' => [
        'external_user_id' => [
            'name' => 'external_user_id',
            'vname' => 'LBL_EXTERNAL_USER_ID',
            'type' => 'id',
            'required' => false,
            'reportable' => false,
            'comment' => 'The external user this person is associated with',
        ],
    ],
    'indices' => [
        'external_user_id' => [
            'name' => 'idx_' . strtolower($table_name) . '_external_user_id',
            'type' => 'index',
            'fields' => ['external_user_id'],
        ],
    ],
];
