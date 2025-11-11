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
$dictionary['Campaign'] = [
    'audited' => true,
    'activity_enabled' => true,
    'color' => 'yellow',
    'icon' => 'sicon-campaigns-lg',
    'comment' => 'Campaigns are a series of operations undertaken to accomplish a purpose, usually acquiring leads',
    'table' => 'campaigns',
    'unified_search' => true,
    'full_text_search' => true,
    'fields' => [
        'tracker_key' => [
            'name' => 'tracker_key',
            'vname' => 'LBL_TRACKER_KEY',
            'type' => 'int',
            'required' => true,
            'studio' => [
                'editview' => false,
            ],
            'len' => '11',
            'auto_increment' => true,
            'comment' => 'The internal ID of the tracker used in a campaign; no longer used as of 4.2 (see campaign_trkrs)',
            'readonly' => true,
        ],
        'tracker_count' => [
            'name' => 'tracker_count',
            'vname' => 'LBL_TRACKER_COUNT',
            'type' => 'int',
            'len' => '11',
            'default' => '0',
            'comment' => 'The number of accesses made to the tracker URL; no longer used as of 4.2 (see campaign_trkrs)',
        ],
        'name' => [
            'name' => 'name',
            'vname' => 'LBL_CAMPAIGN_NAME',
            'dbType' => 'varchar',
            'type' => 'name',
            'len' => '50',
            'comment' => 'The name of the campaign',
            'importable' => 'required',
            'required' => true,
            'unified_search' => true,
            'full_text_search' => [
                'enabled' => true,
                'searchable' => true,
                'boost' => 1.39,
            ],
        ],
        'description' => [
            'name' => 'description',
            'type' => 'none',
            'comment' => 'inhertied but not used',
            'source' => 'non-db',
        ],
        'tracker_text' => [
            'name' => 'tracker_text',
            'vname' => 'LBL_TRACKER_TEXT',
            'type' => 'varchar',
            'len' => '255',
            'comment' => 'The text that appears in the tracker URL; no longer used as of 4.2 (see campaign_trkrs)',
        ],

        'start_date' => [
            'name' => 'start_date',
            'vname' => 'LBL_START_DATE',
            'type' => 'date',
            'audited' => true,
            'comment' => 'Starting date of the campaign',
            'validation' => [
                'type' => 'isbefore',
                'compareto' => 'end_date',
            ],
            'enable_range_search' => true,
            'options' => 'date_range_search_dom',

        ],
        'end_date' => [
            'name' => 'end_date',
            'vname' => 'LBL_END_DATE',
            'type' => 'date',
            'audited' => true,
            'comment' => 'Ending date of the campaign',
            'importable' => 'required',
            'required' => true,
            'enable_range_search' => true,
            'options' => 'date_range_search_dom',

        ],
        'status' => [
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'options' => 'campaign_status_dom',
            'len' => 100,
            'audited' => true,
            'comment' => 'Status of the campaign',
            'importable' => 'required',
            'required' => true,
        ],
        'impressions' => [
            'name' => 'impressions',
            'vname' => 'LBL_CAMPAIGN_IMPRESSIONS',
            'type' => 'int',
            'default' => 0,
            'reportable' => true,
            'comment' => 'Expected Click throughs manually entered by Campaign Manager',
        ],
        'budget' => [
            'name' => 'budget',
            'vname' => 'LBL_CAMPAIGN_BUDGET',
            'type' => 'currency',
            'comment' => 'Budgeted amount for the campaign',
        ],
        'expected_cost' => [
            'name' => 'expected_cost',
            'vname' => 'LBL_CAMPAIGN_EXPECTED_COST',
            'type' => 'currency',
            'comment' => 'Expected cost of the campaign',
        ],
        'actual_cost' => [
            'name' => 'actual_cost',
            'vname' => 'LBL_CAMPAIGN_ACTUAL_COST',
            'type' => 'currency',
            'comment' => 'Actual cost of the campaign',
        ],
        'expected_revenue' => [
            'name' => 'expected_revenue',
            'vname' => 'LBL_CAMPAIGN_EXPECTED_REVENUE',
            'type' => 'currency',
            'comment' => 'Expected revenue stemming from the campaign',
        ],
        'campaign_type' => [
            'name' => 'campaign_type',
            'vname' => 'LBL_TYPE',
            'type' => 'enum',
            'options' => 'campaign_type_dom',
            'len' => 100,
            'audited' => true,
            'comment' => 'The type of campaign',
            'importable' => 'required',
            'required' => true,
            'full_text_search' => [
                'enabled' => true,
                'searchable' => false,
            ],

        ],
        'objective' => [
            'name' => 'objective',
            'vname' => 'LBL_CAMPAIGN_OBJECTIVE',
            'type' => 'text',
            'full_text_search' => [
                'enabled' => true,
                'searchable' => true,
                'boost' => 0.42,
            ],
            'comment' => 'The objective of the campaign',
        ],
        'content' => [
            'name' => 'content',
            'vname' => 'LBL_CAMPAIGN_CONTENT',
            'type' => 'text',
            'full_text_search' => [
                'enabled' => true,
                'searchable' => true,
                'boost' => 0.41,
            ],
            'comment' => 'The campaign description',
        ],
        'prospectlists' => [
            'name' => 'prospectlists',
            'type' => 'link',
            'relationship' => 'prospect_list_campaigns',
            'source' => 'non-db',
            'vname' => 'LBL_PROSPECT_LIST',

        ],
        'emailmarketing' => [
            'name' => 'emailmarketing',
            'type' => 'link',
            'relationship' => 'campaign_email_marketing',
            'source' => 'non-db',

        ],
        'queueitems' => [
            'name' => 'queueitems',
            'type' => 'link',
            'relationship' => 'campaign_emailman',
            'source' => 'non-db',

        ],
        'log_entries' => [
            'name' => 'log_entries',
            'type' => 'link',
            'relationship' => 'campaign_campaignlog',
            'source' => 'non-db',
            'vname' => 'LBL_LOG_ENTRIES',

        ],
        'tracked_urls' => [
            'name' => 'tracked_urls',
            'type' => 'link',
            'relationship' => 'campaign_campaigntrakers',
            'source' => 'non-db',
            'vname' => 'LBL_TRACKED_URLS',

        ],
        'frequency' => [
            'name' => 'frequency',
            'vname' => 'LBL_CAMPAIGN_FREQUENCY',
            'type' => 'enum',
            //'options' => 'campaign_status_dom',
            'len' => 100,
            'comment' => 'Frequency of the campaign',
            'options' => 'newsletter_frequency_dom',
            'len' => 100,

        ],
        'leads' => [
            'name' => 'leads',
            'type' => 'link',
            'relationship' => 'campaign_leads',
            'source' => 'non-db',
            'vname' => 'LBL_LEADS',
            'link_class' => 'ProspectLink',
        ],

        'opportunities' => [
            'name' => 'opportunities',
            'type' => 'link',
            'relationship' => 'campaign_opportunities',
            'source' => 'non-db',
            'vname' => 'LBL_OPPORTUNITIES',

        ],
        'contacts' => [
            'name' => 'contacts',
            'type' => 'link',
            'relationship' => 'campaign_contacts',
            'source' => 'non-db',
            'vname' => 'LBL_CONTACTS',
            'link_class' => 'ProspectLink',
        ],
        'accounts' => [
            'name' => 'accounts',
            'type' => 'link',
            'relationship' => 'campaign_accounts',
            'source' => 'non-db',
            'vname' => 'LBL_ACCOUNTS',
            'link_class' => 'ProspectLink',
        ],
        'forecastworksheet' => [
            'name' => 'forecastworksheet',
            'type' => 'link',
            'relationship' => 'forecastworksheets_campaigns',
            'source' => 'non-db',
            'vname' => 'LBL_FORECAST_WORKSHEET',
        ],


    ],
    'indices' => [
        [
            'name' => 'camp_auto_tracker_key',
            'type' => 'index',
            'fields' => [
                'tracker_key',
            ],
        ],
        ['name' => 'idx_campaign_status', 'type' => 'index', 'fields' => ['status']],
        ['name' => 'idx_campaign_campaign_type', 'type' => 'index', 'fields' => ['campaign_type']],
        ['name' => 'idx_campaign_end_date', 'type' => 'index', 'fields' => ['end_date']],
    ],

    'relationships' => [
        'campaign_accounts' => [
            'lhs_module' => 'Campaigns',
            'lhs_table' => 'campaigns',
            'lhs_key' => 'id',
            'rhs_module' => 'Accounts',
            'rhs_table' => 'accounts',
            'rhs_key' => 'campaign_id',
            'relationship_type' => 'one-to-many',
        ],

        'campaign_contacts' => [
            'lhs_module' => 'Campaigns',
            'lhs_table' => 'campaigns',
            'lhs_key' => 'id',
            'rhs_module' => 'Contacts',
            'rhs_table' => 'contacts',
            'rhs_key' => 'campaign_id',
            'relationship_type' => 'one-to-many',
        ],

        'campaign_products' => [
            'lhs_module' => 'Campaigns',
            'lhs_table' => 'campaigns',
            'lhs_key' => 'id',
            'rhs_module' => 'Products',
            'rhs_table' => 'products',
            'rhs_key' => 'campaign_id',
            'relationship_type' => 'one-to-many',
        ],

        'campaign_revenuelineitems' => [
            'lhs_module' => 'Campaigns',
            'lhs_table' => 'campaigns',
            'lhs_key' => 'id',
            'rhs_module' => 'RevenueLineItems',
            'rhs_table' => 'revenue_line_items',
            'rhs_key' => 'campaign_id',
            'relationship_type' => 'one-to-many',
            'workflow' => false,
        ],

        'campaign_leads' => [
            'lhs_module' => 'Campaigns',
            'lhs_table' => 'campaigns',
            'lhs_key' => 'id',
            'rhs_module' => 'Leads',
            'rhs_table' => 'leads',
            'rhs_key' => 'campaign_id',
            'relationship_type' => 'one-to-many',
        ],

        'campaign_prospects' => [
            'lhs_module' => 'Campaigns',
            'lhs_table' => 'campaigns',
            'lhs_key' => 'id',
            'rhs_module' => 'Prospects',
            'rhs_table' => 'prospects',
            'rhs_key' => 'campaign_id',
            'relationship_type' => 'one-to-many',
        ],

        'campaign_opportunities' => [
            'lhs_module' => 'Campaigns',
            'lhs_table' => 'campaigns',
            'lhs_key' => 'id',
            'rhs_module' => 'Opportunities',
            'rhs_table' => 'opportunities',
            'rhs_key' => 'campaign_id',
            'relationship_type' => 'one-to-many',
        ],

        'campaign_email_marketing' => [
            'lhs_module' => 'Campaigns',
            'lhs_table' => 'campaigns',
            'lhs_key' => 'id',
            'rhs_module' => 'EmailMarketing',
            'rhs_table' => 'email_marketing',
            'rhs_key' => 'campaign_id',
            'relationship_type' => 'one-to-many',
        ],

        'campaign_emailman' => [
            'lhs_module' => 'Campaigns',
            'lhs_table' => 'campaigns',
            'lhs_key' => 'id',
            'rhs_module' => 'EmailMan',
            'rhs_table' => 'emailman',
            'rhs_key' => 'campaign_id',
            'relationship_type' => 'one-to-many',
        ],

        'campaign_campaignlog' => [
            'lhs_module' => 'Campaigns',
            'lhs_table' => 'campaigns',
            'lhs_key' => 'id',
            'rhs_module' => 'CampaignLog',
            'rhs_table' => 'campaign_log',
            'rhs_key' => 'campaign_id',
            'relationship_type' => 'one-to-many',
        ],

        'campaign_assigned_user' => [
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'Campaigns',
            'rhs_table' => 'campaigns',
            'rhs_key' => 'assigned_user_id',
            'relationship_type' => 'one-to-many',
        ],

        'campaign_modified_user' => [
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'Campaigns',
            'rhs_table' => 'campaigns',
            'rhs_key' => 'modified_user_id',
            'relationship_type' => 'one-to-many',
        ],

    ],
];
VardefManager::createVardef(
    'Campaigns',
    'Campaign',
    [
        'default',
        'assignable',
        'team_security',
        'currency',
    ]
);
