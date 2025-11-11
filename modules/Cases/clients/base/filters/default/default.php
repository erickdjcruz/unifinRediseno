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

$viewdefs['Cases']['base']['filter']['default'] = [
    'default_filter' => 'all_records',
    'quicksearch_field' => ['name', 'case_number',],
    'quicksearch_priority' => 2,
    'fields' => [
        'name' => [],
        'account_name' => [],
        'status' => [],
        'priority' => [],
        'case_number' => [],
        'date_entered' => [],
        'date_modified' => [],
        'tag' => [],
        'request_close' => [],
        'request_close_date' => [],
        'follow_up_datetime' => [],
        'assigned_user_name' => [],
        '$owner' => [
            'predefined_filter' => true,
            'vname' => 'LBL_CURRENT_USER_FILTER',
        ],
        '$favorite' => [
            'predefined_filter' => true,
            'vname' => 'LBL_FAVORITES_FILTER',
        ],
        'is_escalated' => [],
        'type' => [],
        'source' => [],
        'primary_contact_name' => [],
        'resolved_datetime' => [],
        'service_level' => [],
        'business_center_name' => [],
        'team_name' => [],
        'modified_by_name' => [],
        'created_by_name' => [],
    ],
];
