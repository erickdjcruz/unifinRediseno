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

$dictionary['PushNotification'] = [
    'table' => 'push_notifications',
    'fields' => [
        'is_sent' => [
            'name' => 'is_sent',
            'vname' => 'LBL_IS_SENT',
            'type' => 'bool',
            'default' => 0,
            'comments' => 'Whether notification is sent or not.',
        ],
        'notification_type' => [
            'name' => 'notification_type',
            'type' => 'varchar',
            'len' => 255,
            'vname' => 'LBL_NOTIFICATION_TYPE',
            'comment' => 'Type of notification, eg, record_assignment.',
        ],
        'parent_type' => [
            'name' => 'parent_type',
            'vname' => 'LBL_PARENT_TYPE',
            'type' => 'parent_type',
            'dbType' => 'varchar',
            'group' => 'parent_name',
            'options' => 'parent_type_display',
            'len' => 100,
            'comment' => 'Module notification is associated with.',
        ],
        'parent_id' => [
            'name' => 'parent_id',
            'vname' => 'LBL_PARENT_ID',
            'type' => 'id',
            'group' => 'parent_name',
            'reportable' => false,
            'comment' => 'ID of item indicated by parent_type.',
        ],
        'extra_data' => [
            'name' => 'extra_data',
            'type' => 'text',
            'vname' => 'LBL_EXTRA_DATA',
            'comment' => 'JSON encoded string containing extra data.',
        ],
    ],
    'uses' => [
        'basic',
        'assignable',
    ],
    'ignore_templates' => [
        'taggable',
        'commentlog',
    ],
    'visibility' => [
        'OwnerOrAdminVisibility' => true,
    ],
];

VardefManager::createVardef('PushNotifications', 'PushNotification');
