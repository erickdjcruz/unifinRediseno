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

$dictionary['folders'] = [
    'table' => 'folders',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
            'required' => true,
        ],
        'name' => [
            'name' => 'name',
            'type' => 'varchar',
            'len' => 25,
            'required' => true,
        ],
        'folder_type' => [
            'name' => 'folder_type',
            'type' => 'varchar',
            'len' => 25,
            'default' => null,
        ],
        'parent_folder' => [
            'name' => 'parent_folder',
            'type' => 'id',
            'required' => false,
        ],
        'has_child' => [
            'name' => 'has_child',
            'type' => 'bool',
            'default' => '0',
        ],
        'is_group' => [
            'name' => 'is_group',
            'type' => 'bool',
            'default' => '0',
        ],
        'is_dynamic' => [
            'name' => 'is_dynamic',
            'type' => 'bool',
            'default' => '0',
        ],
        'dynamic_query' => [
            'name' => 'dynamic_query',
            'type' => 'text',
        ],
        'assign_to_id' => [
            'name' => 'assign_to_id',
            'type' => 'id',
            'required' => false,
        ],
        'team_id' => [
            'name' => 'team_id',
            'type' => 'id',
            'required' => false,
        ],
        'team_set_id' => [
            'name' => 'team_set_id',
            'type' => 'id',
            'required' => false,
        ],
        'acl_team_set_id' => [
            'name' => 'acl_team_set_id',
            'type' => 'id',
            'required' => false,
        ],
        'created_by' => [
            'name' => 'created_by',
            'type' => 'id',
            'required' => true,
        ],
        'modified_by' => [
            'name' => 'modified_by',
            'type' => 'id',
            'required' => true,
        ],
        'deleted' => [
            'name' => 'deleted',
            'type' => 'bool',
            'default' => '0',
        ],
    ],
    'indices' => [
        [
            'name' => 'folderspk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_parent_folder',
            'type' => 'index',
            'fields' => [
                'parent_folder',
            ],
        ],
    ],
];

$dictionary['folders_subscriptions'] = [
    'table' => 'folders_subscriptions',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
            'required' => true,
        ],
        'folder_id' => [
            'name' => 'folder_id',
            'type' => 'id',
            'required' => true,
        ],
        'assigned_user_id' => [
            'name' => 'assigned_user_id',
            'type' => 'id',
            'required' => true,
        ],
    ],
    'indices' => [
        [
            'name' => 'folders_subscriptionspk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_folder_id_assigned_user_id',
            'type' => 'index',
            'fields' => [
                'folder_id',
                'assigned_user_id',
            ],
        ],
    ],
];

$dictionary['folders_rel'] = [
    'table' => 'folders_rel',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
            'required' => true,
        ],
        'folder_id' => [
            'name' => 'folder_id',
            'type' => 'id',
            'required' => true,
        ],
        'polymorphic_module' => [
            'name' => 'polymorphic_module',
            'type' => 'varchar',
            'len' => 25,
            'required' => true,
        ],
        'polymorphic_id' => [
            'name' => 'polymorphic_id',
            'type' => 'id',
            'required' => true,
        ],
        'deleted' => [
            'name' => 'deleted',
            'type' => 'bool',
            'default' => '0',
        ],
    ],
    'indices' => [
        [
            'name' => 'folders_relpk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_poly_module_poly_id',
            'type' => 'index',
            'fields' => [
                'polymorphic_module',
                'polymorphic_id',
            ],
        ],
        [
            'name' => 'idx_fr_id_deleted_poly',
            'type' => 'index',
            'fields' => [
                'folder_id',
                'deleted',
                'polymorphic_id',
            ],
        ],
    ],
];
