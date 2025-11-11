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

$vardefs = [
    'fields' => [
        'my_favorite' => [
            'massupdate' => false,
            'name' => 'my_favorite',
            'vname' => 'LBL_FAVORITE',
            'type' => 'bool',
            'source' => 'non-db',
            'comment' => 'Favorite for the user',
            'studio' => [
                // This field is on sidecar list and record views by default
                'list' => false,
                'recordview' => false,
                'basic_search' => false,
                'advanced_search' => false,
            ],
            'link' => 'favorite_link',
            'rname' => 'id',
            'rname_exists' => true,
        ],
        'favorite_link' => [
            'name' => 'favorite_link',
            'type' => 'link',
            'relationship' => strtolower($module) . '_favorite',
            'source' => 'non-db',
            'vname' => 'LBL_FAVORITE',
            'reportable' => false,
            'workflow' => false,
            'full_text_search' => [
                'type' => 'favorites',
                'enabled' => true,
                'searchable' => false,
                'aggregations' => [
                    'favorite_link' => [
                        'type' => 'MyItems',
                        'options' => [
                            'field' => 'user_favorites',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'relationships' => [
        strtolower($module) . '_favorite' => [
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => $module,
            'rhs_table' => $table_name,
            'rhs_key' => 'id',
            'relationship_type' => 'user-based',
            'join_table' => 'sugarfavorites',
            'join_key_lhs' => 'modified_user_id',
            'join_key_rhs' => 'record_id',
            'relationship_role_column' => 'module',
            'relationship_role_column_value' => $module,
            'user_field' => 'created_by',
        ],
    ],
    'indices' => [
    ],
];
