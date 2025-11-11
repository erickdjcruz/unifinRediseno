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

$layout_defs['Calls'] = [
    // sets up which panels to show, in which order, and with what linked_fields
    'subpanel_setup' => [
        'contacts' => [
            'top_buttons' => [],
            'order' => 10,
            'module' => 'Contacts',
            'sort_order' => 'asc',
            'sort_by' => 'last_name, first_name',
            'subpanel_name' => 'ForCalls',
            'get_subpanel_data' => 'contacts',
            'title_key' => 'LBL_CONTACTS_SUBPANEL_TITLE',
        ],
        'users' => [
            'top_buttons' => [],
            'order' => 20,
            'module' => 'Users',
            'sort_order' => 'asc',
            'sort_by' => 'full_name',
            'subpanel_name' => 'ForCalls',
            'get_subpanel_data' => 'users',
            'title_key' => 'LBL_USERS_SUBPANEL_TITLE',
        ],
        'leads' => [
            'order' => 30,
            'module' => 'Leads',
            'sort_order' => 'asc',
            'sort_by' => 'last_name, first_name',
            'subpanel_name' => 'ForCalls',
            'get_subpanel_data' => 'leads',
            'title_key' => 'LBL_LEADS_SUBPANEL_TITLE',
            'top_buttons' => [],
        ],
        'history' => [
            'order' => 40,
            'title_key' => 'LBL_HISTORY_SUBPANEL_TITLE',
            'type' => 'collection',
            'subpanel_name' => 'history',   //this values is not associated with a physical file.
            'sort_order' => 'desc',
            'sort_by' => 'date_entered',
            'header_definition_from_subpanel' => 'calls',
            'module' => 'History',
            'top_buttons' => [
                ['widget_class' => 'SubPanelTopCreateNoteButton'],
            ],
            'collection_list' => [
                'notes' => [
                    'module' => 'Notes',
                    'subpanel_name' => 'ForCalls',
                    'get_subpanel_data' => 'notes',
                ],
            ],
        ], /* end history subpanel def */
    ],
];
