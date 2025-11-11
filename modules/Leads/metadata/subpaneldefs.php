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


$layout_defs['Leads'] = [
    // sets up which panels to show, in which order, and with what linked_fields
    'subpanel_setup' => [
        'activities' => [
            'order' => 20,
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
                'meetings_parent' => [
                    'module' => 'Meetings',
                    'subpanel_name' => 'ForActivities',
                    'get_subpanel_data' => 'function:get_old_related_meetings',
                    'set_subpanel_data' => 'meetings_parent',
                    'generate_select' => true,
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
                'calls_parent' => [
                    'module' => 'Calls',
                    'subpanel_name' => 'ForActivities',
                    'get_subpanel_data' => 'function:get_old_related_calls',
                    'set_subpanel_data' => 'calls_parent',
                    'generate_select' => true,
                ],
            ],
        ],
        'history' => [
            'order' => 30,
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
                'meetings_parent' => [
                    'module' => 'Meetings',
                    'subpanel_name' => 'ForHistory',
                    'get_subpanel_data' => 'function:get_old_related_meetings',
                    'generate_select' => true,
                    'set_subpanel_data' => 'meetings_parent',
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
                'calls_parent' => [
                    'module' => 'Calls',
                    'subpanel_name' => 'ForHistory',
                    'get_subpanel_data' => 'function:get_old_related_calls',
                    'set_subpanel_data' => 'calls_parent',
                    'generate_select' => true,
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
        'campaigns' => [
            'order' => 40,
            'module' => 'CampaignLog',
            'sort_order' => 'desc',
            'sort_by' => 'activity_date',
            'get_subpanel_data' => 'campaigns',
            'subpanel_name' => 'ForTargets',
            'title_key' => 'LBL_CAMPAIGN_LIST_SUBPANEL_TITLE',
        ],
    ],
];
