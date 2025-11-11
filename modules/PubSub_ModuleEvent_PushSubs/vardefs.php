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

$dictionary['PubSub_ModuleEvent_PushSub'] = [
    'acls' => [
        'SugarACLAdminOnly' => true,
    ],
    'activity_enabled' => false,
    'audited' => false,
    'comment' => 'Pub/Sub module event subscriptions',
    'duplicate_check' => [
        'enabled' => false,
    ],
    'duplicate_merge' => false,
    'favorites' => false,
    'fields' => [
        'expiration_date' => [
            'comment' => 'Subscriptions last 7 days and must be updated to be extended',
            'duplicate_on_record_copy' => 'no',
            'exportable' => false,
            'isnull' => false,
            'mandatory_fetch' => true,
            'massupdate' => false,
            'name' => 'expiration_date',
            'readonly' => true,
            'reportable' => false,
            'required' => true,
            'studio' => false,
            'type' => 'datetime',
            'vname' => 'LBL_EXPIRATION_DATE',
        ],
        'target_module' => [
            'comment' => 'Send notifications regarding this module',
            'duplicate_on_record_copy' => 'no',
            'exportable' => false,
            'isnull' => false,
            'mandatory_fetch' => true,
            'massupdate' => false,
            'name' => 'target_module',
            'readonly' => true,
            'reportable' => false,
            'required' => true,
            'studio' => false,
            'type' => 'varchar',
            'vname' => 'LBL_TARGET_MODULE',
        ],
        'token' => [
            'comment' => 'An arbitrary string delivered to the destination with each notification',
            'duplicate_on_record_copy' => 'no',
            'exportable' => false,
            'isnull' => false,
            'mandatory_fetch' => true,
            'massupdate' => false,
            'name' => 'token',
            'readonly' => true,
            'reportable' => false,
            'required' => true,
            'studio' => false,
            'type' => 'varchar',
            'vname' => 'LBL_TOKEN',
        ],
        'webhook_url' => [
            'comment' => 'Send notifications to this webhook',
            'duplicate_on_record_copy' => 'no',
            'exportable' => false,
            'isnull' => false,
            'mandatory_fetch' => true,
            'massupdate' => false,
            'name' => 'webhook_url',
            'reportable' => false,
            'required' => true,
            'studio' => false,
            'type' => 'varchar',
            'vname' => 'LBL_WEBHOOK_URL',
        ],
    ],
    'full_text_search' => false,
    'indices' => [
        'idx_pubsub_moduleevent_pushsubs_target_module_expiration_date' => [
            'name' => 'idx_pubsub_moduleevent_pushsubs_target_module_expiration_date',
            'type' => 'index',
            'fields' => [
                'target_module',
                'expiration_date',
            ],
        ],
    ],
    'optimistic_locking' => true,
    'table' => 'pubsub_moduleevent_pushsubs',
    'unified_search' => false,
    'unified_search_default_enabled' => false,
    'uses' => [
        'default',
    ],
];

VardefManager::createVardef(
    'PubSub_ModuleEvent_PushSubs',
    'PubSub_ModuleEvent_PushSub'
);

$dictionary['PubSub_ModuleEvent_PushSub']['fields']['description']['full_text_search'] = [
    'enabled' => false,
];
