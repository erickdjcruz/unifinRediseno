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


$layout_defs['ProspectLists'] = [
    // list of what Subpanels to show in the DetailView
    'subpanel_setup' => [
        'prospects' => [
            'order' => 10,
            'sort_by' => 'last_name',
            'sort_order' => 'asc',
            'module' => 'Prospects',
            'subpanel_name' => 'default',
            'get_subpanel_data' => 'prospects',
            'title_key' => 'LBL_PROSPECTS_SUBPANEL_TITLE',
            'top_buttons' => [
                ['widget_class' => 'SubPanelTopButtonQuickCreate'],
                ['widget_class' => 'SubPanelTopSelectButton', 'mode' => 'MultiSelect'],
                ['widget_class' => 'SubPanelTopSelectFromReportButton'],
            ],
        ],
        'contacts' => [
            'order' => 20,
            'module' => 'Contacts',
            'sort_by' => 'last_name, first_name',
            'sort_order' => 'asc',
            'subpanel_name' => 'default',
            'get_subpanel_data' => 'contacts',
            'title_key' => 'LBL_CONTACTS_SUBPANEL_TITLE',
            'top_buttons' => [
                ['widget_class' => 'SubPanelTopButtonQuickCreate'],
                ['widget_class' => 'SubPanelTopSelectButton', 'mode' => 'MultiSelect'],
                ['widget_class' => 'SubPanelTopSelectFromReportButton'],
            ],
        ],
        'leads' => [
            'order' => 30,
            'module' => 'Leads',
            'sort_by' => 'last_name, first_name',
            'sort_order' => 'asc',
            'subpanel_name' => 'default',
            'get_subpanel_data' => 'leads',
            'title_key' => 'LBL_LEADS_SUBPANEL_TITLE',
            'top_buttons' => [
                ['widget_class' => 'SubPanelTopButtonQuickCreate'],
                ['widget_class' => 'SubPanelTopSelectButton', 'mode' => 'MultiSelect'],
                ['widget_class' => 'SubPanelTopSelectFromReportButton'],
            ],
        ],
        'users' => [
            'order' => 40,
            'module' => 'Users',
            'sort_by' => 'name',
            'sort_order' => 'asc',
            'subpanel_name' => 'ForProspectLists',
            'get_subpanel_data' => 'users',
            'title_key' => 'LBL_USERS_SUBPANEL_TITLE',
            'top_buttons' => [
                ['widget_class' => 'SubPanelTopSelectButton', 'mode' => 'MultiSelect'],
                ['widget_class' => 'SubPanelTopSelectFromReportButton'],
            ],
        ],
        'accounts' => [
            'order' => 40,
            'module' => 'Accounts',
            'sort_order' => 'asc',
            'sort_by' => 'name',
            'subpanel_name' => 'ForProspectLists',
            'get_subpanel_data' => 'accounts',
            'add_subpanel_data' => 'account_id',
            'title_key' => 'LBL_ACCOUNTS_SUBPANEL_TITLE',
            'top_buttons' => [
                ['widget_class' => 'SubPanelTopButtonQuickCreate'],
                ['widget_class' => 'SubPanelTopSelectButton', 'mode' => 'MultiSelect'],
                ['widget_class' => 'SubPanelTopSelectFromReportButton'],
            ],
        ],
    ],
];
