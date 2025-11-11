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
$dictionary['InboundEmail_autoreply'] = ['table' => 'inbound_email_autoreply',
    'fields' => [
        'id' => [
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'id',
            'required' => true,
            'reportable' => false,
        ],
        'deleted' => [
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'required' => false,
            'default' => '0',
            'reportable' => false,
        ],
        'date_entered' => [
            'name' => 'date_entered',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
            'required' => true,
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_MODIFIED',
            'type' => 'datetime',
            'required' => true,
        ],
        'autoreplied_to' => [
            'name' => 'autoreplied_to',
            'vname' => 'LBL_AUTOREPLIED_TO',
            'type' => 'varchar',
            'len' => 100,
            'required' => true,
            'reportable' => false,
        ],
        'ie_id' => [
            'name' => 'ie_id',
            'vname' => 'LBL_INBOUNDEMAIL_ID',
            'type' => 'id',
            'default' => '',
            'required' => true,
            'reportable' => false,
        ],
    ],
    'indices' => [
        [
            'name' => 'ie_autopk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_ie_autoreplied_to',
            'type' => 'index',
            'fields' => [
                'autoreplied_to',
            ],
        ],
    ], /* end indices */
    'relationships' => [
    ], /* end relationships */
];
