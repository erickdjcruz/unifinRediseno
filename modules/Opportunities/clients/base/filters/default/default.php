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

$viewdefs['Opportunities']['base']['filter']['default'] = [
    'default_filter' => 'all_records',
    'fields' => [
        'name' => [],
        'account_name' => [],
        'amount' => [],
        'renewal' => [],
        'next_step' => [],
        'probability' => [],
        'lead_source' => [],
        'opportunity_type' => [],
        'sales_stage' => [],
        'date_entered' => [],
        'date_modified' => [],
        'date_closed' => [],
        'service_duration' => [
            'dbFields' => [
                'service_duration_value',
                'service_duration_unit',
            ],
            'vname' => 'LBL_SERVICE_DURATION',
            'type' => 'text',
        ],
        'tag' => [],
        'assigned_user_name' => [],
        '$owner' => [
            'predefined_filter' => true,
            'vname' => 'LBL_CURRENT_USER_FILTER',
        ],
        '$favorite' => [
            'predefined_filter' => true,
            'vname' => 'LBL_FAVORITES_FILTER',
        ],
        '$distance' => [
            'name' => '$distance',
            'vname' => 'LBL_MAPS_DISTANCE',
            'type' => 'maps-distance',
            'source' => 'non-db',
            'merge_filter' => 'enabled',
            'licenseFilter' => ['MAPS'],
        ],
        'is_escalated' => [],
        'forecasted_likely' => [],
        'commit_stage' => [],
        'lost' => [],
        'team_name' => [],
        'modified_by_name' => [],
        'created_by_name' => [],
    ],
];
