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

$dictionary['Activity'] = [
    'table' => 'activities',
    'fields' => [
        // Set unnecessary fields from Basic to non-required/non-db.
        'name' => [
            'name' => 'name',
            'type' => 'varchar',
            'required' => false,
            'source' => 'non-db',
        ],

        'description' => [
            'name' => 'description',
            'type' => 'varchar',
            'required' => false,
            'source' => 'non-db',
        ],

        // Add relationship fields.
        'comments' => [
            'name' => 'comments',
            'type' => 'link',
            'relationship' => 'comments',
            'link_type' => 'many',
            'module' => 'Comments',
            'bean_name' => 'Comment',
            'source' => 'non-db',
        ],

        'activities_users' => [
            'name' => 'activities_users',
            'type' => 'link',
            'relationship' => 'activities_users',
            'link_type' => 'many',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
        ],

        'activities_teams' => [
            'name' => 'activities_teams',
            'type' => 'link',
            'relationship' => 'activities_teams',
            'link_type' => 'many',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
        ],

        // Relationships for M2M related beans.
        'contacts' => [
            'name' => 'contacts',
            'type' => 'link',
            'relationship' => 'contact_activities',
            'vname' => 'LBL_LIST_CONTACT_NAME',
            'source' => 'non-db',
        ],
        'cases' => [
            'name' => 'cases',
            'type' => 'link',
            'relationship' => 'case_activities',
            'vname' => 'LBL_CASES',
            'source' => 'non-db',
        ],
        'accounts' => [
            'name' => 'accounts',
            'type' => 'link',
            'relationship' => 'account_activities',
            'source' => 'non-db',
            'vname' => 'LBL_ACCOUNTS',
        ],
        'opportunities' => [
            'name' => 'opportunities',
            'type' => 'link',
            'relationship' => 'opportunity_activities',
            'source' => 'non-db',
            'vname' => 'LBL_OPPORTUNITIES',
        ],
        'quotas' => [
            'name' => 'quotas',
            'type' => 'link',
            'relationship' => 'quota_activities',
            'source' => 'non-db',
            'vname' => 'LBL_QUOTAS',
        ],
        'leads' => [
            'name' => 'leads',
            'type' => 'link',
            'relationship' => 'lead_activities',
            'source' => 'non-db',
            'vname' => 'LBL_LEADS',
        ],
        'products' => [
            'name' => 'products',
            'type' => 'link',
            'relationship' => 'product_activities',
            'source' => 'non-db',
            'vname' => 'LBL_PRODUCTS',
        ],
        'revenuelineitems' => [
            'name' => 'revenuelineitems',
            'type' => 'link',
            'relationship' => 'revenuelineitem_activities',
            'source' => 'non-db',
            'vname' => 'LBL_REVENUELINEITEMS',
            'workflow' => false,
        ],
        'quotes' => [
            'name' => 'quotes',
            'type' => 'link',
            'relationship' => 'quote_activities',
            'vname' => 'LBL_QUOTES',
            'source' => 'non-db',
        ],
        'contracts' => [
            'name' => 'contracts',
            'type' => 'link',
            'relationship' => 'contract_activities',
            'source' => 'non-db',
            'vname' => 'LBL_CONTRACTS',
        ],
        'bugs' => [
            'name' => 'bugs',
            'type' => 'link',
            'relationship' => 'bug_activities',
            'source' => 'non-db',
            'vname' => 'LBL_BUGS',
        ],
        'meetings' => [
            'name' => 'meetings',
            'type' => 'link',
            'relationship' => 'meeting_activities',
            'source' => 'non-db',
            'vname' => 'LBL_MEETINGS',
        ],
        'calls' => [
            'name' => 'calls',
            'type' => 'link',
            'relationship' => 'call_activities',
            'source' => 'non-db',
            'vname' => 'LBL_CALLS',
        ],
        'tasks' => [
            'name' => 'tasks',
            'type' => 'link',
            'relationship' => 'task_activities',
            'source' => 'non-db',
            'vname' => 'LBL_TASKS',
        ],
        'notes' => [
            'name' => 'notes',
            'type' => 'link',
            'relationship' => 'note_activities',
            'source' => 'non-db',
            'vname' => 'LBL_NOTES',
        ],
        'kbcontents' => [
            'name' => 'kbcontents',
            'type' => 'link',
            'relationship' => 'kbcontent_activities',
            'source' => 'non-db',
            'vname' => 'LBL_KBCONTENTS',
        ],
        'kbtemplates' => [
            'name' => 'kbtemplates',
            'type' => 'link',
            'relationship' => 'kbcontenttemplate_activities',
            'source' => 'non-db',
            'vname' => 'LBL_KBTEMPLATES',
        ],
        'campaigns' => [
            'name' => 'campaigns',
            'type' => 'link',
            'relationship' => 'campaign_activities',
            'source' => 'non-db',
            'vname' => 'LBL_CAMPAIGN',
        ],

        'pmse_Project' => [
            'name' => 'pmse_Project',
            'type' => 'link',
            'relationship' => 'pmse_project_activities',
            'source' => 'non-db',
            'vname' => 'LBL_PMSE_PROJECT_ACTIVITIES_TITLE',
        ],
        'pmse_Business_Rules' => [
            'name' => 'pmse_Business_Rules',
            'type' => 'link',
            'relationship' => 'pmse_business_rules_activities',
            'source' => 'non-db',
            'vname' => 'LBL_PMSE_BUSINESS_RULES_ACTIVITIES_TITLE',
        ],
        'pmse_Emails_Templates' => [
            'name' => 'pmse_Emails_Templates',
            'type' => 'link',
            'relationship' => 'pmse_emails_templates_activities',
            'source' => 'non-db',
            'vname' => 'LBL_PMSE_EMAILS_TEMPLATES_ACTIVITIES_TITLE',
        ],
        'purchasedlineitems' => [
            'name' => 'purchasedlineitems',
            'type' => 'link',
            'relationship' => 'purchasedlineitem_activities',
            'source' => 'non-db',
            'vname' => 'LBL_PLIS_ACTIVITIES',
        ],

        // Add table columns.
        'parent_id' => [
            'name' => 'parent_id',
            'type' => 'id',
        ],

        'parent_type' => [
            'name' => 'parent_type',
            'type' => 'varchar',
            'len' => 100,
        ],

        'activity_type' => [
            'name' => 'activity_type',
            'type' => 'varchar',
            'len' => 100,
            'required' => true,
        ],

        'data' => [
            'name' => 'data',
            'type' => 'json',
            'dbType' => 'longtext',
            'required' => true,
        ],

        'comment_count' => [
            'name' => 'comment_count',
            'type' => 'int',
            'required' => true,
            'default' => 0,
        ],

        'last_comment' => [
            'name' => 'last_comment',
            'type' => 'json',
            'dbType' => 'longtext',
            'required' => true,
        ],
    ],
    'indices' => [
        [
            'name' => 'activity_records',
            'type' => 'index',
            'fields' => ['parent_type', 'parent_id'],
        ],
        [
            'name' => 'activity_type_date',
            'type' => 'index',
            'fields' => ['activity_type', 'date_entered'],
        ],
    ],
    'relationships' => [
        'comments' => [
            'lhs_module' => 'Activities',
            'lhs_table' => 'activities',
            'lhs_key' => 'id',
            'rhs_module' => 'Comments',
            'rhs_table' => 'comments',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
        ],
    ],
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

VardefManager::createVardef('ActivityStream/Activities', 'Activity', ['basic']);

//Need to override the relationship because lhs_module is populed with ActivityStream/Activities instead of module name Activities
$dictionary['Activity']['relationships']['activity_activities']['lhs_module'] = 'Activities';
