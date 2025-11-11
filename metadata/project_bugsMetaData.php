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

$dictionary['projects_bugs'] = [
    'table' => 'projects_bugs',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'bug_id' => [
            'name' => 'bug_id',
            'type' => 'id',
        ],
        'project_id' => [
            'name' => 'project_id',
            'type' => 'id',
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'type' => 'datetime',
        ],
        'deleted' => [
            'name' => 'deleted',
            'type' => 'bool',
            'len' => '1',
            'default' => '0',
            'required' => false,
        ],
    ],
    'indices' => [
        [
            'name' => 'projects_bugs_pk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_proj_bug_bug',
            'type' => 'index',
            'fields' => [
                'bug_id',
            ],
        ],
        [
            'name' => 'projects_bugs_alt',
            'type' => 'alternate_key',
            'fields' => [
                'project_id',
                'bug_id',
            ],
        ],
    ],
    'relationships' => [
        'projects_bugs' => [
            'lhs_module' => 'Project',
            'lhs_table' => 'project',
            'lhs_key' => 'id',
            'rhs_module' => 'Bugs',
            'rhs_table' => 'bugs',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'projects_bugs',
            'join_key_lhs' => 'project_id',
            'join_key_rhs' => 'bug_id',
        ],
    ],
];
