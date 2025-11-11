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

$dictionary['projects_opportunities'] = [
    'table' => 'projects_opportunities',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'opportunity_id' => [
            'name' => 'opportunity_id',
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
            'name' => 'projects_opportunities_pk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_proj_opp_opp',
            'type' => 'index',
            'fields' => [
                'opportunity_id',
            ],
        ],
        [
            'name' => 'projects_opportunities_alt',
            'type' => 'alternate_key',
            'fields' => [
                'project_id',
                'opportunity_id',
            ],
        ],
    ],
    'relationships' => [
        'projects_opportunities' => [
            'lhs_module' => 'Project',
            'lhs_table' => 'project',
            'lhs_key' => 'id',
            'rhs_module' => 'Opportunities',
            'rhs_table' => 'opportunities',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'projects_opportunities',
            'join_key_lhs' => 'project_id',
            'join_key_rhs' => 'opportunity_id',
        ],
    ],
];
