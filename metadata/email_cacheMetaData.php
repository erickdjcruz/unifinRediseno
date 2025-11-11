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
 * Relationship table linking emails with 1 or more SugarBeans
 */
$dictionary['email_cache'] = [
    'table' => 'email_cache',
    'fields' => [
        'ie_id' => [
            'name' => 'ie_id',
            'type' => 'id',
        ],
        'mbox' => [
            'name' => 'mbox',
            'type' => 'varchar',
            'len' => 60,
            'required' => true,
        ],
        'subject' => [
            'name' => 'subject',
            'type' => 'varchar',
            'len' => 255,
            'required' => false,
        ],
        'fromaddr' => [
            'name' => 'fromaddr',
            'type' => 'varchar',
            'len' => 100,
            'required' => false,
        ],
        'toaddr' => [
            'name' => 'toaddr',
            'type' => 'varchar',
            'len' => 255,
            'required' => false,
        ],
        'senddate' => [
            'name' => 'senddate',
            'type' => 'datetime',
            'required' => false,
        ],
        'message_id' => [
            'name' => 'message_id',
            'type' => 'varchar',
            'len' => 255,
            'required' => false,
        ],
        'mailsize' => [
            'name' => 'mailsize',
            'type' => 'uint',
            'len' => 16,
            'required' => true,
        ],
        'imap_uid' => [
            'name' => 'imap_uid',
            'type' => 'uint',
            'len' => 32,
            'required' => true,
        ],
        'msgno' => [
            'name' => 'msgno',
            'type' => 'uint',
            'len' => 32,
            'required' => false,
        ],
        'recent' => [
            'name' => 'recent',
            'type' => 'tinyint',
            'len' => 1,
            'required' => true,
        ],
        'flagged' => [
            'name' => 'flagged',
            'type' => 'tinyint',
            'len' => 1,
            'required' => true,
        ],
        'answered' => [
            'name' => 'answered',
            'type' => 'tinyint',
            'len' => 1,
            'required' => true,
        ],
        'deleted' => [
            'name' => 'deleted',
            'type' => 'tinyint',
            'len' => 1,
            'required' => false,
        ],
        'seen' => [
            'name' => 'seen',
            'type' => 'tinyint',
            'len' => 1,
            'required' => true,
        ],
        'draft' => [
            'name' => 'draft',
            'type' => 'tinyint',
            'len' => 1,
            'required' => true,
        ],
    ],
    'indices' => [
        [
            'name' => 'idx_mail_date',
            'type' => 'index',
            'fields' => [
                'ie_id',
                'mbox',
                'senddate',
            ],
        ],
        [
            'name' => 'idx_mail_from',
            'type' => 'index',
            'fields' => [
                'ie_id',
                'mbox',
                'fromaddr',
            ],
        ],
        [
            'name' => 'idx_mail_subj',
            'type' => 'index',
            'fields' => [
                'subject',
            ],
        ],
        [
            'name' => 'idx_mail_to',
            'type' => 'index',
            'fields' => [
                'toaddr',
            ],
        ],

    ],
];
