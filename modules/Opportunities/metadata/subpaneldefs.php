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


$layout_defs['Opportunities'] = [
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
                'meetings' => [
                    'module' => 'Meetings',
                    'subpanel_name' => 'ForActivities',
                    'get_subpanel_data' => 'meetings',
                ],
                'tasks' => [
                    'module' => 'Tasks',
                    'subpanel_name' => 'ForActivities',
                    'get_subpanel_data' => 'tasks',
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
                'meetings' => [
                    'module' => 'Meetings',
                    'subpanel_name' => 'ForHistory',
                    'get_subpanel_data' => 'meetings',
                ],
                'tasks' => [
                    'module' => 'Tasks',
                    'subpanel_name' => 'ForHistory',
                    'get_subpanel_data' => 'tasks',
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
                    'subpanel_name' => 'ForUnlinkedEmailHistory',
                    'get_subpanel_data' => 'function:get_emails_by_assign_or_link',
                    'function_parameters' => ['import_function_file' => 'include/utils.php', 'link' => 'contacts'],
                    'generate_select' => true,
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
            'order' => 50,
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
        'contacts' => [
            'order' => 30,
            'module' => 'Contacts',
            'sort_order' => 'asc',
            'sort_by' => 'last_name, first_name',
            'subpanel_name' => 'ForOpportunities',
            'get_subpanel_data' => 'contacts',
            'add_subpanel_data' => 'contact_id',
            'title_key' => 'LBL_CONTACTS_SUBPANEL_TITLE',
            'top_buttons' => [
                ['widget_class' => 'SubPanelTopCreateAccountNameButton'],
                ['widget_class' => 'SubPanelTopSelectButton',
                    'popup_module' => 'Opportunities',
                    'mode' => 'MultiSelect',
                    'initial_filter_fields' => ['account_id' => 'account_id', 'account_name' => 'account_name'],
                ],
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
            'top_buttons' => [
                ['widget_class' => 'SubPanelTopCreateButton'],
                ['widget_class' => 'SubPanelTopSelectButton', 'mode' => 'MultiSelect'],
            ],
        ],

        'project' => [
            'order' => 70,
            'module' => 'Project',
            'get_subpanel_data' => 'project',
            'sort_order' => 'asc',
            'sort_by' => 'name',
            'subpanel_name' => 'default',
            'title_key' => 'LBL_PROJECTS_SUBPANEL_TITLE',
            'top_buttons' => [
                ['widget_class' => 'SubPanelTopButtonQuickCreate'],
                ['widget_class' => 'SubPanelTopSelectButton', 'mode' => 'MultiSelect'],
            ],
        ],

        'revenuelineitems' => [
            'order' => 60,
            'module' => 'RevenueLineItems',
            'subpanel_name' => 'ForOpportunities',
            'sort_order' => 'desc',
            'sort_by' => 'date_purchased',
            'title_key' => 'LBL_RLI_SUBPANEL_TITLE',
            'get_subpanel_data' => 'revenuelineitems',
            'top_buttons' => [
                [
                    'widget_class' => 'SubPanelTopButtonQuickCreate',
                ],
                [
                    'widget_class' => 'SubPanelTopSelectButton',
                    'mode' => 'MultiSelect',
                ],
            ],

        ],

        'contracts' => [
            'order' => 60,
            'module' => 'Contracts',
            'sort_order' => 'desc',
            'sort_by' => 'end_date',
            'subpanel_name' => 'default',
            'get_subpanel_data' => 'contracts',
            'add_subpanel_data' => 'contract_id',
            'title_key' => 'LBL_CONTRACTS_SUBPANEL_TITLE',
            'top_buttons' => [
                ['widget_class' => 'SubPanelTopButtonQuickCreate'],
                ['widget_class' => 'SubPanelTopSelectButton', 'popup_module' => 'Contracts', 'mode' => 'MultiSelect',
                    'initial_filter_fields' => ['account_id' => 'account_id', 'account_name' => 'account_name']],
            ],
        ],
    ],
];
