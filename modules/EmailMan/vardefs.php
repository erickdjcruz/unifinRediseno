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
$dictionary['EmailMan'] =
    ['table' => 'emailman', 'archive' => false, 'comment' => 'Email campaign queue', 'fields' => [
        'date_entered' => [
            'name' => 'date_entered',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
            'comment' => 'Date record created',
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_MODIFIED',
            'type' => 'datetime',
            'comment' => 'Date record last modified',
        ],
        'user_id' => [
            'name' => 'user_id',
            'vname' => 'LBL_USER_ID',
            'type' => 'id',
            'reportable' => false,
            'comment' => 'User ID representing assigned-to user',
        ],
        'id' => [
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'int',
            'len' => '11',
            'auto_increment' => true,
            'readonly' => true,
            'comment' => 'Unique identifier',
        ],
        'campaign_id' => [
            'name' => 'campaign_id',
            'vname' => 'LBL_CAMPAIGN_ID',
            'type' => 'id',
            'reportable' => false,
            'comment' => 'ID of related campaign',
        ],
        'marketing_id' => [
            'name' => 'marketing_id',
            'vname' => 'LBL_MARKETING_ID',
            'type' => 'id',
            'reportable' => false,
            'comment' => '',
        ],
        'list_id' => [
            'name' => 'list_id',
            'vname' => 'LBL_LIST_ID',
            'type' => 'id',
            'reportable' => false,
            'comment' => 'Associated list',
        ],
        'send_date_time' => [
            'name' => 'send_date_time',
            'vname' => 'LBL_SEND_DATE_TIME',
            'type' => 'datetime',
        ],
        'modified_user_id' => [
            'name' => 'modified_user_id',
            'vname' => 'LBL_MODIFIED_USER_ID',
            'type' => 'id',
            'reportable' => false,
            'comment' => 'User ID who last modified record',
        ],
        'in_queue' => [
            'name' => 'in_queue',
            'vname' => 'LBL_IN_QUEUE',
            'type' => 'bool',
            'default' => '0',
            'comment' => 'Flag indicating if item still in queue',
        ],
        'in_queue_date' => [
            'name' => 'in_queue_date',
            'vname' => 'LBL_IN_QUEUE_DATE',
            'type' => 'datetime',
            'comment' => 'Datetime in which item entered queue',
        ],
        'send_attempts' => [
            'name' => 'send_attempts',
            'vname' => 'LBL_SEND_ATTEMPTS',
            'type' => 'int',
            'default' => '0',
            'comment' => 'Number of attempts made to send this item',
        ],
        'deleted' => [
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'reportable' => false,
            'comment' => 'Record deletion indicator',
            'default' => '0',
        ],
        'related_id' => [
            'name' => 'related_id',
            'vname' => 'LBL_RELATED_ID',
            'type' => 'id',
            'reportable' => false,
            'comment' => 'ID of Sugar object to which this item is related',
        ],
        'related_type' => [
            'name' => 'related_type',
            'vname' => 'LBL_RELATED_TYPE',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'Descriptor of the Sugar object indicated by related_id',
        ],
        'recipient_name' => [
            'name' => 'recipient_name',
            'type' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
        ],
        'recipient_email' => [
            'name' => 'recipient_email',
            'type' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
        ],
        'message_name' => [
            'name' => 'message_name',
            'type' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
        ],
        'campaign_name' => [
            'name' => 'campaign_name',
            'vname' => 'LBL_LIST_CAMPAIGN',
            'type' => 'varchar',
            'len' => '50',
            'source' => 'non-db',
        ],

    ], 'indices' => [
        ['name' => 'emailmanpk', 'type' => 'primary', 'fields' => ['id']],
        ['name' => 'idx_eman_list', 'type' => 'index', 'fields' => ['list_id', 'user_id', 'deleted']],
        ['name' => 'idx_eman_campaign_id', 'type' => 'index', 'fields' => ['campaign_id']],
        ['name' => 'idx_emailman_send_date_time', 'type' => 'index', 'fields' => ['send_date_time']],
        ['name' => 'idx_emailman_send_attempts', 'type' => 'index', 'fields' => ['send_attempts']],
    ],
    ];
