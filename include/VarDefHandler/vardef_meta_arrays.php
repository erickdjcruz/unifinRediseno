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

//holds various filter arrays for displaying vardef dropdowns
//You can add your own if you would like

$vardef_meta_array = [
    // Things that should be handled for ALL cases
    'all' => [
        'inclusion' => [],
        'exclusion' => [
            'module' => [
                'DataPrivacy',
            ],
        ],
        'inc_override' => [],
        'ex_override' => [
            'name' => [
                // Remove the following fields from all lists
                'pmse_bpmnactivity_link',
                'pmse_bpmnartifact_link',
                'pmse_bpmnbound_link',
                'pmse_bpmndata_link',
                'pmse_bpmndiagram_link',
                'pmse_bpmndocumentation_link',
                'pmse_bpmnevent_link',
                'pmse_bpmnextension_link',
                'pmse_bpmnflow_link',
                'pmse_bpmngateway_link',
                'pmse_bpmnlane_link',
                'pmse_bpmnlaneset_link',
                'pmse_bpmnparticipant_link',
                'pmse_bpmnprocess_link',
                'pmse_bpmflow_link',
                'pmse_bpmthread_link',
                'pmse_bpmnotes_link',
                'pmse_bpmrelateddependency_link',
                'pmse_bpmactivityuser_link',
                'pmse_bpmeventdefinition_link',
                'pmse_bpmgatewaydefinition_link',
                'pmse_bpmactivitydefinition_link',
                'pmse_bpmactivitystep_link',
                'pmse_bpmformaction_link',
                'pmse_bpmdynaform_link',
                'pmse_bpmprocessdefinition_link',
                'pmse_bpmconfig_link',
                'pmse_bpmgroup_link',
                'pmse_bpmgroupuser_link',
                'dataprivacy',
            ],
            'module' => [
                'DataPrivacy',
            ],
        ],
    ],
    'standard_display' => [
        'inclusion' => [//end inclusion
        ],
        'exclusion' => [
            'type' => ['id'],
            'name' => ['parent_type', 'deleted'],
            'workflow' => [false],
            //end exclusion
        ],
        'inc_override' => [
            'type' => ['team_list'],
            //end inc_override
        ],
        'ex_override' => [//end ex_override
        ],
        //end standard_display
    ],
//////////////////////////////////////////////////////////////////
    'normal_trigger' => [
        'inclusion' => [//end inclusion
        ],
        'exclusion' => [
            'type' => ['id', 'link', 'datetime', 'date', 'datetimecombo'],
            'custom_type' => ['id', 'link', 'datetime', 'date', 'datetimecombo'],
            'name' => ['assigned_user_name', 'parent_type', 'deleted', 'filename', 'file_mime_type', 'file_url'],
            'source' => ['non-db'],
            'workflow' => [false],
            //end exclusion
        ],
        'inc_override' => [
            'type' => ['team_list', 'assigned_user_name'],
            'name' => ['email1', 'assigned_user_id'],
            //end inc_override
        ],
        'ex_override' => [
            'name' => ['team_name'],
            //end ex_override
        ],

        //end normal_trigger
    ],
    //////////////////////////////////////////////////////////////////
    'normal_date_trigger' => [
        'inclusion' => [//end inclusion
        ],
        'exclusion' => [
            'type' => ['id', 'link'],
            'custom_type' => ['id', 'link'],
            'name' => ['assigned_user_name', 'parent_type', 'deleted', 'filename', 'file_mime_type', 'file_url'],
            'source' => ['non-db'],
            'workflow' => [false],
            //end exclusion
        ],
        'inc_override' => [
            'type' => ['team_list', 'assigned_user_name'],
            'name' => ['email1', 'assigned_user_id'],
            //end inc_override
        ],
        'ex_override' => [
            'name' => ['team_name', 'account_name'],
            //end ex_override
        ],

        //end normal_trigger
    ],
//////////////////////////////////////////////////////////////////
    'time_trigger' => [
        'inclusion' => [//end inclusion
        ],
        'exclusion' => [
            'type' => ['id', 'link', 'team_list', 'time'],
            'custom_type' => ['id', 'link', 'team_list', 'time'],
            'workflow' => [false],
            'name' => [
                'parent_type',
                'team_name',
                'assigned_user_name',
                'parent_type',
                'deleted',
                'filename',
                'file_mime_type',
                'file_url',
            ],
            'source' => ['non-db'],
            //end exclusion
        ],
        'inc_override' => [//end inc_override
        ],
        'ex_override' => [
            'name' => ['date_entered'],
            //end ex_override
        ],

        //end time_trigger
    ],
//////////////////////////////////////////////////////////////////
    'action_filter' => [
        'inclusion' => [//end inclusion
        ],
        'exclusion' => [
            'type' => ['id', 'link', 'datetime', 'time'],
            'custom_type' => ['id', 'link', 'datetime', 'time'],
            'source' => ['non-db'],
            'name' => [
                'created_by',
                'parent_type',
                'deleted',
                'assigned_user_name',
                'deleted',
                'filename',
                'file_mime_type',
                'file_url',
                'resource_id',
            ],
            'readonly' => [true],
            'workflow' => [false],
            'auto_increment' => [true],
            'calculated' => [true],
            //end exclusion
        ],
        'inc_override' => [
            'type' => ['team_list'],
            'name' => [
                'assigned_user_id',
                'time_start',
                'date_start',
                'email1',
                'date_due',
                'is_optout',
            ],
            //end inc_override
        ],
        'ex_override' => [
            'name' => ['team_name', 'account_name'],
            //end ex_override
        ],

        //end action_filter
    ],
//////////////////////////////////////////////////////////////////
    'rel_filter' => [
        'inclusion' => [
            'type' => ['link'],
            //end inclusion
        ],
        'exclusion' => [
            'name' => [
                'direct_reports',
                'accept_status',
                'team_count_link',
                'activities',
                'team_link',
                'email_addresses_primary',
                'email_addresses',
                'archived_emails',
                'reportees',
                'tasks_parent',
                'locked_fields_link',
            ],
            'module' => [
                'Forecasts',
                'Documents',
                'Products',
                'CampaignLog',
            ],
            'workflow' => [false],
            //end exclusion
        ],
        'inc_override' => [
            'name' => ['accounts', 'account', 'member_of'],
            //end inc_override
        ],
        'ex_override' => [
            //'link_type' => array('one'),
            'name' => ['users'],
            'module' => ['Users'],
            //end ex_override
        ],

        //end rel_filter
    ],
///////////////////////////////////////////////////////////
    'trigger_rel_filter' => [
        'inclusion' => [
            'type' => ['link'],
            //end inclusion
        ],
        'exclusion' => [
            'name' => ['direct_reports', 'accept_status', 'archived_emails'],
            'workflow' => [false],
            //end exclusion
        ],
        'inc_override' => [
            'name' => [],
            //end inc_override
        ],
        'ex_override' => [
            'name' => [
                'users',
                'emails',
                'product_bundles',
                'email_addresses',
                'email_addresses_primary',
                'emailmarketing',
                'tracked_urls',
                'queueitems',
                'log_entries',
                'contract_types',
                'locked_fields_link',
            ],
            'module' => [
                'Users',
                'Teams',
                'CampaignLog',
            ],
            //end ex_override
        ],

        //end trigger_rel_filter
    ],
///////////////////////////////////////////////////////////
    'alert_rel_filter' => [
        'inclusion' => [
            'type' => ['link'],
            //end inclusion
        ],
        'exclusion' => [
            'name' => ['direct_reports', 'accept_status', 'archived_emails'],
            'workflow' => [false],
            //end exclusion
        ],
        'inc_override' => [
            'name' => [],
            //end inc_override
        ],
        'ex_override' => [
            'name' => [
                'users',
                'emails',
                'product_bundles',
                'email_addresses',
                'email_addresses_primary',
                'emailmarketing',
                'tracked_urls',
                'queueitems',
                'log_entries',
                'contract_types',
                'reports_to_link',
                'locked_fields_link',
            ],
            'module' => [
                'Users',
                'Teams',
                'CampaignLog',
                'Releases',
            ],
            //end ex_override
        ],

        //end alert_rel_filter
    ],
///////////////////////////////////////////////////////////
    'template_filter' => [
        'inclusion' => [//end inclusion
        ],
        'exclusion' => [
            'type' => ['id', 'link'],
            'custom_type' => ['id', 'link'],
            'source' => ['non-db'],
            'workflow' => [false],
            'name' => [
                'created_by',
                'parent_type',
                'deleted',
                'assigned_user_name',
                'filename',
                'file_mime_type',
                'file_url',
            ],
            //end exclusion
        ],
        'inc_override' => [
            'name' => [
                'assigned_user_id',
                'assigned_user_name',
                'modified_user_id',
                'modified_by_name',
                'created_by',
                'created_by_name',
                'full_name',
                'email1',
                'team_name',
                'shipper_name',
            ],
            'type' => [
                'relate',
            ],
            //end inc_override
        ],
        'ex_override' => [
            'name' => ['team_id'],
            //end ex_override
        ],

        //end template_filter
    ],
//////////////////////////////////////////////////////////////
    'alert_trigger' => [
        'inclusion' => [//end inclusion
        ],
        'exclusion' => [
            'type' => ['id', 'link', 'datetime', 'date'],
            'custom_type' => ['id', 'link', 'datetime', 'date'],
            'name' => ['assigned_user_name', 'parent_type', 'deleted', 'filename', 'file_mime_type', 'file_url'],
            'source' => ['non-db'],
            'workflow' => [false],
            //end exclusion
        ],
        'inc_override' => [
            'type' => ['team_list', 'assigned_user_name'],
            'name' => ['full_name'],
            //end inc_override
        ],
        'ex_override' => [
            'name' => ['team_name', 'account_name'],
            //end ex_override
        ],

        //end alert_trigger
    ],
//////////////////////////////////////////////////////////////////
    'template_rel_filter' => [
        'inclusion' => [
            'type' => ['link'],
            //end inclusion
        ],
        'exclusion' => [
            'name' => ['direct_reports', 'accept_status'],
            'workflow' => [false],
            //end exclusion
        ],
        'inc_override' => [
            'name' => [],
            //end inc_override
        ],
        'ex_override' => [
            'name' => [
                'users',
                'email_addresses',
                'email_addresses_primary',
                'emailmarketing',
                'tracked_urls',
                'queueitems',
                'log_entries',
                'reports_to_link',
            ],
            'module' => [
                'Users',
                'Teams',
                'CampaignLog',
            ],
            //end ex_override
        ],

        //end template_rel_filter
    ],
];
