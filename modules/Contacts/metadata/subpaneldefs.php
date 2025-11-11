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

$layout_defs['Contacts'] = [
    // list of what Subpanels to show in the DetailView
    'subpanel_setup' => [

        'activities' => [
            'order' => 10,
            'sort_order' => 'desc',
            'sort_by' => 'date_start',
            'title_key' => 'LBL_ACTIVITIES_SUBPANEL_TITLE',
            'type' => 'collection',
            'subpanel_name' => 'activities',   //this values is not associated with a physical file.
            'module' => 'Activities',

            'top_buttons' => [
                ['widget_class' => 'SubPanelTopCreateTaskButton'],
                ['widget_class' => 'SubPanelTopScheduleMeetingButton'],
                ['widget_class' => 'SubPanelTopScheduleCallButton'],
                ['widget_class' => 'SubPanelTopComposeEmailButton'],
            ],
            'collection_list' => [
                'tasks' => [
                    'module' => 'Tasks',
                    'subpanel_name' => 'ForActivities',
                    'get_subpanel_data' => 'tasks',
                ],
                'tasks_parent' => [
                    'module' => 'Tasks',
                    'subpanel_name' => 'ForActivities',
                    'get_subpanel_data' => 'tasks_parent',
                ],
                'meetings' => [
                    'module' => 'Meetings',
                    'subpanel_name' => 'ForActivities',
                    'get_subpanel_data' => 'meetings',
                ],
                'calls' => [
                    'module' => 'Calls',
                    'subpanel_name' => 'ForActivities',
                    'get_subpanel_data' => 'calls',
                ],
            ],
        ],

        'history' => [
            'order' => 20,
            'sort_order' => 'desc',
            'sort_by' => 'date_entered',
            'title_key' => 'LBL_HISTORY_SUBPANEL_TITLE',
            'type' => 'collection',
            'subpanel_name' => 'history',   //this values is not associated with a physical file.
            'module' => 'History',

            'top_buttons' => [
                ['widget_class' => 'SubPanelTopCreateNoteButton'],
                ['widget_class' => 'SubPanelTopArchiveEmailButton'],
                ['widget_class' => 'SubPanelTopSummaryButton'],
            ],

            'collection_list' => [
                'tasks' => [
                    'module' => 'Tasks',
                    'subpanel_name' => 'ForHistory',
                    'get_subpanel_data' => 'tasks',
                ],
                'tasks_parent' => [
                    'module' => 'Tasks',
                    'subpanel_name' => 'ForHistory',
                    'get_subpanel_data' => 'tasks_parent',
                ],
                'meetings' => [
                    'module' => 'Meetings',
                    'subpanel_name' => 'ForHistory',
                    'get_subpanel_data' => 'meetings',
                ],
                'calls' => [
                    'module' => 'Calls',
                    'subpanel_name' => 'ForHistory',
                    'get_subpanel_data' => 'calls',
                ],
                'notes' => [
                    'module' => 'Notes',
                    'subpanel_name' => 'ForHistory',
                    'get_subpanel_data' => 'notes',
                ],
                'emails' => [
                    'module' => 'Emails',
                    'subpanel_name' => 'ForHistory',
                    'get_subpanel_data' => 'emails',
                ],
                'linkedemails' => [
                    'module' => 'Emails',
                    'subpanel_name' => 'ForUnlinkedEmailHistory',
                    'get_subpanel_data' => 'function:get_unlinked_email_query',
                    'generate_select' => true,
                    'function_parameters' => ['return_as_array' => 'true'],
                ],
            ],
        ],
        'documents' => [
            'order' => 25,
            'module' => 'Documents',
            'subpanel_name' => 'default',
            'sort_order' => 'asc',
            'sort_by' => 'id',
            'title_key' => 'LBL_DOCUMENTS_SUBPANEL_TITLE',
            'get_subpanel_data' => 'documents',
            'top_buttons' => [
                0 =>
                    [
                        'widget_class' => 'SubPanelTopButtonQuickCreate',
                    ],
                1 =>
                    [
                        'widget_class' => 'SubPanelTopSelectButton',
                        'mode' => 'MultiSelect',
                    ],
            ],
        ],
        'leads' => [
            'order' => 60,
            'module' => 'Leads',
            'sort_order' => 'asc',
            'sort_by' => 'last_name, first_name',
            'subpanel_name' => 'default',
            'get_subpanel_data' => 'leads',
            'add_subpanel_data' => 'lead_id',
            'title_key' => 'LBL_LEADS_SUBPANEL_TITLE',
            'top_buttons' => [
                ['widget_class' => 'SubPanelTopCreateLeadNameButton'],
                ['widget_class' => 'SubPanelTopSelectButton',
                    'popup_module' => 'Opportunities',
                    'mode' => 'MultiSelect',
                ],
            ],
        ],
        'opportunities' => [
            'order' => 30,
            'module' => 'Opportunities',
            'sort_order' => 'desc',
            'sort_by' => 'date_closed',
            'subpanel_name' => 'default',
            'get_subpanel_data' => 'opportunities',
            'add_subpanel_data' => 'opportunity_id',
            'title_key' => 'LBL_OPPORTUNITIES_SUBPANEL_TITLE',
            'top_buttons' => [
                ['widget_class' => 'SubPanelTopButtonQuickCreate'],
                ['widget_class' => 'SubPanelTopSelectButton', 'mode' => 'MultiSelect'],
            ],
        ],
        'quotes' => [
            'order' => 40,
            'module' => 'Quotes',
            'sort_order' => 'desc',
            'sort_by' => 'date_quote_expected_closed',
            'subpanel_name' => 'default',
            'get_subpanel_data' => 'quotes',
            'add_subpanel_data' => 'quote_id',
            'title_key' => 'LBL_QUOTES_SUBPANEL_TITLE',
            'get_distinct_data' => true,
            'top_buttons' => [
                ['widget_class' => 'SubPanelTopCreateButton'],
            ],
        ],
        'cases' => [
            'order' => 80,
            'sort_order' => 'desc',
            'sort_by' => 'case_number',
            'module' => 'Cases',
            'subpanel_name' => 'default',
            'get_subpanel_data' => 'cases',
            'add_subpanel_data' => 'case_id',
            'title_key' => 'LBL_CASES_SUBPANEL_TITLE',
            'top_buttons' => [
                ['widget_class' => 'SubPanelTopButtonQuickCreate'],
                ['widget_class' => 'SubPanelTopSelectButton', 'mode' => 'MultiSelect'],
            ],
        ],
        'bugs' => [
            'order' => 90,
            'module' => 'Bugs',
            'sort_order' => 'desc',
            'sort_by' => 'bug_number',
            'subpanel_name' => 'default',
            'get_subpanel_data' => 'bugs',
            'add_subpanel_data' => 'bug_id',
            'title_key' => 'LBL_BUGS_SUBPANEL_TITLE',
            'top_buttons' => [
                ['widget_class' => 'SubPanelTopButtonQuickCreate'],
                ['widget_class' => 'SubPanelTopSelectButton', 'mode' => 'MultiSelect'],
            ],
        ],
        'contacts' => [
            'order' => 100,
            'module' => 'Contacts',
            'sort_order' => 'asc',
            'sort_by' => 'last_name, first_name',
            'subpanel_name' => 'ForContacts',
            'get_subpanel_data' => 'direct_reports',
            'add_subpanel_data' => 'contact_id',
            'title_key' => 'LBL_DIRECT_REPORTS_SUBPANEL_TITLE',
            'top_buttons' => [
                ['widget_class' => 'SubPanelTopButtonQuickCreate'],
                ['widget_class' => 'SubPanelTopSelectButton', 'mode' => 'MultiSelect'],
            ],
        ],
        'project' => [
            'order' => 110,
            'module' => 'Project',
            'sort_order' => 'asc',
            'sort_by' => 'name',
            'get_subpanel_data' => 'project',
            'subpanel_name' => 'default',
            'title_key' => 'LBL_PROJECTS_SUBPANEL_TITLE',
            'top_buttons' => [
                ['widget_class' => 'SubPanelTopButtonQuickCreate'],
                ['widget_class' => 'SubPanelTopSelectButton', 'mode' => 'MultiSelect'],
            ],
        ],
        'campaigns' => [
            'order' => 70,
            'module' => 'CampaignLog',
            'sort_order' => 'desc',
            'sort_by' => 'activity_date',
            'get_subpanel_data' => 'campaigns',
            'subpanel_name' => 'ForTargets',
            'title_key' => 'LBL_CAMPAIGN_LIST_SUBPANEL_TITLE',
        ],
        'contracts' => [
            'order' => 120,
            'sort_order' => 'desc',
            'sort_by' => 'end_date',
            'module' => 'Contracts',
            'subpanel_name' => 'default',
            'get_subpanel_data' => 'contracts',
            'add_subpanel_data' => 'contract_id',
            'title_key' => 'LBL_CONTRACTS_SUBPANEL_TITLE',
        ],
    ],
];
