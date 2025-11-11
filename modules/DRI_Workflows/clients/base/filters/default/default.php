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
$viewdefs['DRI_Workflows']['base']['filter']['default'] = [
    'default_filter' => 'all_records',
    'fields' => [
        'name' => [],
        'available_modules' => [],
        'assigned_user_name' => [],
        'account_name' => [],
        'opportunity_name' => [],
        'contact_name' => [],
        'case_name' => [],
        'lead_name' => [],
        'dri_workflow_template_name' => [],
        'points' => [],
        'assignee_rule' => [],
        'score' => [],
        'archived' => [],
        'target_assignee' => [],
        'current_stage_name' => [],
        'state' => [],
        '$owner' => [
            'predefined_filter' => true,
            'vname' => 'LBL_CURRENT_USER_FILTER',
        ],
        '$favorite' => [
            'predefined_filter' => true,
            'vname' => 'LBL_FAVORITES_FILTER',
        ],
        'team_name' => [],
    ],
];
