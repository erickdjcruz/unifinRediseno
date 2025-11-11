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
 * table storing reports filter information */
$dictionary['report_cache'] = [
    'table' => 'report_cache',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
            'required' => true,
        ],
        'assigned_user_id' => [
            'name' => 'assigned_user_id',
            'type' => 'id',
            'required' => true,
        ],
        'contents' => [
            'name' => 'contents',
            'type' => 'text',
            'comment' => 'contents of report object',
            'default' => null,
        ],
        'report_options' => [
            'name' => 'report_options',
            'type' => 'text',
            'comment' => 'options of report object like hide details, hide shart etc..',
            'default' => null,
        ],
        'deleted' => [
            'name' => 'deleted',
            'type' => 'varchar',
            'len' => 1,
            'required' => true,
        ],
        'date_entered' => [
            'name' => 'date_entered',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
            'required' => true,
            'comment' => 'Date record created',
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_MODIFIED',
            'type' => 'datetime',
            'required' => true,
            'comment' => 'Date record last modified',
        ],
    ],
    'indices' => [
        [
            'name' => 'report_cache_pk',
            'type' => 'primary',
            'fields' => ['id', 'assigned_user_id'],
        ],
    ],
];
