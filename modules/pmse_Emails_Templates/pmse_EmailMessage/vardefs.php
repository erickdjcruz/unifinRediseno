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

$dictionary['pmse_EmailMessage'] = [
    'table' => 'pmse_email_message',
    'archive' => false,
    'audited' => false,
    'activity_enabled' => false,
    'reassignable' => false,
    'duplicate_merge' => true,
    'fields' => [
        'from_addr' => [
            'name' => 'from_addr',
            'vname' => 'LBL_FROM',
            'type' => 'varchar',
            'len' => 255,
            'comment' => 'Email address of person who send the email',
        ],
        'from_name' => [
            'name' => 'from_name',
            'vname' => 'LBL_FROM_NAME',
            'type' => 'varchar',
            'len' => 255,
            'comment' => 'Name of the sender',
        ],
        'reply_to_addr' => [
            'name' => 'reply_to_addr',
            'vname' => 'LBL_REPLY_TO',
            'type' => 'varchar',
            'len' => 255,
            'comment' => 'Email address of the account the recipient should reply to',
        ],
        'reply_to_name' => [
            'name' => 'reply_to_name',
            'vname' => 'LBL_REPLY_TO_NAME',
            'type' => 'varchar',
            'len' => 255,
            'comment' => 'Name of the account the recipient should reply to',
        ],
        'to_addrs' => [
            'name' => 'to_addrs',
            'vname' => 'LBL_TO',
            'type' => 'text',
            'comment' => 'Email address(es) of person(s) to receive the email',
        ],
        'cc_addrs' => [
            'name' => 'cc_addrs',
            'vname' => 'LBL_CC',
            'type' => 'text',
            'comment' => 'Email address(es) of person(s) to receive a carbon copy of the email',
        ],
        'bcc_addrs' => [
            'name' => 'bcc_addrs',
            'vname' => 'LBL_BCC',
            'type' => 'text',
            'comment' => 'Email address(es) of person(s) to receive a blind carbon copy of the email',
        ],
        'body' => [
            'name' => 'body',
            'vname' => 'LBL_TEXT_BODY',
            'type' => 'longtext',
            'reportable' => false,
            'comment' => 'Email body in plain text',
        ],
        'body_html' => [
            'name' => 'body_html',
            'vname' => 'LBL_HTML_BODY',
            'type' => 'longhtml',
            'reportable' => false,
            'comment' => 'Email body in HTML format',
        ],
        'subject' => [
            'name' => 'subject',
            'vname' => 'LBL_SUBJECT',
            'type' => 'name',
            'dbType' => 'varchar',
            'len' => 255,
        ],
        'flow_id' => [
            'name' => 'flow_id',
            'vname' => 'LBL_FLOW_ID',
            'type' => 'id',
        ],
        'deleted' => [
            'name' => 'deleted',
            'type' => 'bool',
            'default' => 0,
        ],
        'outbound_email_id' => [
            'name' => 'outbound_email_id',
            'type' => 'id',
            'vname' => 'LBL_OUTBOUND_EMAIL_ID',
        ],
        'doc_merge_attach_id' => [
            'name' => 'doc_merge_attach_id',
            'type' => 'id',
        ],
    ],
    'relationships' => [],
    'optimistic_locking' => true,
    'ignore_templates' => [
        'taggable',
        'lockable_fields',
        'commentlog',
    ],
];

VardefManager::createVardef('pmse_EmailMessage', 'pmse_EmailMessage');
