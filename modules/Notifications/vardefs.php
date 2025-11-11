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

$dictionary['Notifications'] = [
    'table' => 'notifications',
    'fields' => [
        'is_read' => [
            'required' => false,
            'name' => 'is_read',
            'vname' => 'LBL_IS_READ',
            'type' => 'bool',
            'massupdate' => true,
            'comments' => '',
            'help' => '',
            'importable' => 'false',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'default' => 0,
            'reportable' => 1,
        ],
        'severity' => [
            'len' => 15,
            'name' => 'severity',
            'options' => 'notifications_severity_list',
            'required' => true,
            'type' => 'enum',
            'massupdate' => false,
            'vname' => 'LBL_SEVERITY',
            'readonly' => true,
        ],
        'parent_name' => [
            'name' => 'parent_name',
            'parent_type' => 'record_type_display',
            'type_name' => 'parent_type',
            'id_name' => 'parent_id',
            'vname' => 'LBL_LIST_RELATED_TO',
            'type' => 'parent',
            'group' => 'parent_name',
            'source' => 'non-db',
            'options' => 'parent_type_display',
            'studio' => true,
            'massupdate' => false,
            'readonly' => true,
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
            'studio' => ['searchview' => false, 'wirelesslistview' => false],
        ],
        'parent_id' => [
            'name' => 'parent_id',
            'vname' => 'LBL_PARENT_ID',
            'type' => 'id',
            'group' => 'parent_name',
            'reportable' => false,
            'comment' => 'ID of item indicated by parent_type.',
            'studio' => ['searchview' => false],
        ],
    ],
    'indices' => [
        [
            'name' => 'idx_notifications_my_unread_items',
            'type' => 'index',
            'fields' => [
                'assigned_user_id',
                'is_read',
                'deleted',
            ],
        ],
        [
            'name' => 'idx_del_parentid_type_isread',
            'type' => 'index',
            'fields' => [
                'deleted',
                'parent_id',
                'parent_type',
                'is_read',
            ],
        ],
    ],
    'relationships' => [],
    'optimistic_lock' => true,
    // @TODO Fix the Default and Basic SugarObject templates so that Basic
    // implements Default. This would allow the application of various
    // implementations on Basic without forcing Default to have those so that
    // situations like this - implementing taggable - doesn't have to apply to
    // EVERYTHING. Since there is no distinction between basic and default for
    // sugar objects templates yet, we need to forecefully remove the taggable
    // implementation fields. Once there is a separation of default and basic
    // templates we can safely remove these as this module will implement
    // default instead of basic.
    'ignore_templates' => [
        'taggable',
        'commentlog',
    ],
];

VardefManager::createVardef('Notifications', 'Notifications', ['basic', 'assignable']);

$dictionary['Notifications']['fields']['assigned_user_name']['massupdate'] = false;
$dictionary['Notifications']['fields']['assigned_user_name']['readonly'] = true;
$dictionary['Notifications']['fields']['description']['readonly'] = true;
$dictionary['Notifications']['fields']['name']['readonly'] = true;
