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


$layout_defs['Documents'] = [
    // list of what Subpanels to show in the DetailView
    'subpanel_setup' => [
        'revisions' => [
            'order' => 10,
            'sort_order' => 'desc',
            'sort_by' => 'revision',
            'module' => 'DocumentRevisions',
            'subpanel_name' => 'default',
            'title_key' => 'LBL_DOC_REV_HEADER',
            'get_subpanel_data' => 'revisions',
            'fill_in_additional_fields' => true,
        ],
        'contracts' => [
            'order' => 20,
            'sort_order' => 'desc',
            'sort_by' => 'name',
            'module' => 'Contracts',
            'subpanel_name' => 'ForDocuments',
            'get_subpanel_data' => 'contracts',
            'add_subpanel_data' => 'contract_id',
            'title_key' => 'LBL_CONTRACTS_SUBPANEL_TITLE',
            'top_buttons' => [],
        ],
        'accounts' => [
            'order' => 30,
            'module' => 'Accounts',
            'subpanel_name' => 'default',
            'sort_order' => 'asc',
            'sort_by' => 'id',
            'title_key' => 'LBL_ACCOUNTS_SUBPANEL_TITLE',
            'get_subpanel_data' => 'accounts',
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
        'contacts' => [
            'order' => 40,
            'module' => 'Contacts',
            'subpanel_name' => 'default',
            'sort_order' => 'asc',
            'sort_by' => 'id',
            'title_key' => 'LBL_CONTACTS_SUBPANEL_TITLE',
            'get_subpanel_data' => 'contacts',
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
        'opportunities' => [
            'order' => 40,
            'module' => 'Opportunities',
            'subpanel_name' => 'default',
            'sort_order' => 'asc',
            'sort_by' => 'id',
            'title_key' => 'LBL_OPPORTUNITIES_SUBPANEL_TITLE',
            'get_subpanel_data' => 'opportunities',
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
        'cases' => [
            'order' => 50,
            'module' => 'Cases',
            'subpanel_name' => 'default',
            'sort_order' => 'asc',
            'sort_by' => 'id',
            'title_key' => 'LBL_CASES_SUBPANEL_TITLE',
            'get_subpanel_data' => 'cases',
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
        'bugs' => [
            'order' => 60,
            'module' => 'Bugs',
            'subpanel_name' => 'default',
            'sort_order' => 'asc',
            'sort_by' => 'id',
            'title_key' => 'LBL_BUGS_SUBPANEL_TITLE',
            'get_subpanel_data' => 'bugs',
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
        'quotes' => [
            'order' => 70,
            'module' => 'Quotes',
            'subpanel_name' => 'default',
            'sort_order' => 'asc',
            'sort_by' => 'id',
            'title_key' => 'LBL_QUOTES_SUBPANEL_TITLE',
            'get_subpanel_data' => 'quotes',
            'top_buttons' => [
                0 =>
                    [
                        'widget_class' => 'SubPanelTopSelectButton',
                        'mode' => 'MultiSelect',
                    ],
            ],
        ],
        'revenuelineitems' => [
            'order' => 80,
            'module' => 'RevenueLineItems',
            'subpanel_name' => 'default',
            'sort_order' => 'asc',
            'sort_by' => 'id',
            'title_key' => 'LBL_RLI_SUBPANEL_TITLE',
            'get_subpanel_data' => 'revenuelineitems',
            'top_buttons' => [
                0 =>
                    [
                        'widget_class' => 'SubPanelTopSelectButton',
                        'mode' => 'MultiSelect',
                    ],
            ],
        ],
        'purchases' => [
            'order' => 90,
            'module' => 'Purchases',
            'subpanel_name' => 'default',
            'sort_order' => 'asc',
            'sort_by' => 'id',
            'title_key' => 'LBL_PURCHASES_SUBPANEL_TITLE',
            'get_subpanel_data' => 'purchases',
            'top_buttons' => [
                0 =>
                    [
                        'widget_class' => 'SubPanelTopSelectButton',
                        'mode' => 'MultiSelect',
                    ],
            ],
        ],
        'purchasedlineitems' => [
            'order' => 90,
            'module' => 'PurchasedLineItems',
            'subpanel_name' => 'default',
            'sort_order' => 'asc',
            'sort_by' => 'id',
            'title_key' => 'LBL_PLIS_SUBPANEL_TITLE',
            'get_subpanel_data' => 'purchasedlineitems',
            'top_buttons' => [
                0 =>
                    [
                        'widget_class' => 'SubPanelTopSelectButton',
                        'mode' => 'MultiSelect',
                    ],
            ],
        ],
    ],
];
