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

$dictionary['prospect_list_campaigns'] = [
    'table' => 'prospect_list_campaigns',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'prospect_list_id' => [
            'name' => 'prospect_list_id',
            'type' => 'id',
        ],
        'campaign_id' => [
            'name' => 'campaign_id',
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
        ],
    ],
    'indices' => [
        [
            'name' => 'prospect_list_campaignspk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_cam_id',
            'type' => 'index',
            'fields' => [
                'campaign_id',
            ],
        ],
        [
            'name' => 'idx_prospect_list_campaigns',
            'type' => 'alternate_key',
            'fields' => [
                'prospect_list_id',
                'campaign_id',
            ],
        ],
    ],
    'relationships' => [
        'prospect_list_campaigns' => [
            'lhs_module' => 'ProspectLists',
            'lhs_table' => 'prospect_lists',
            'lhs_key' => 'id',
            'rhs_module' => 'Campaigns',
            'rhs_table' => 'campaigns',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'prospect_list_campaigns',
            'join_key_lhs' => 'prospect_list_id',
            'join_key_rhs' => 'campaign_id',
        ],
    ],
];
