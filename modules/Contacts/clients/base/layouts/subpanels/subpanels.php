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
$viewdefs['Contacts']['base']['layout']['subpanels'] = [
    'components' => [
        [
            'layout' => 'subpanel',
            'label' => 'LBL_CALLS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'calls',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_MEETINGS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'meetings',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_MESSAGES_SUBPANEL_TITLE',
            'context' => [
                'link' => 'message_invites',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_TASKS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'all_tasks',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_NOTES_SUBPANEL_TITLE',
            'context' => [
                'link' => 'notes',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_LEADS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'leads',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_OPPORTUNITIES_SUBPANEL_TITLE',
            'override_subpanel_list_view' => 'subpanel-for-contacts',
            'context' => [
                'link' => 'opportunities',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_EMAILS_SUBPANEL_TITLE',
            'override_subpanel_list_view' => 'subpanel-for-contacts-archived-emails',
            'context' => [
                'link' => 'archived_emails',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_CASES_SUBPANEL_TITLE',
            'context' => [
                'link' => 'cases',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_BUGS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'bugs',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_DIRECT_REPORTS_SUBPANEL_TITLE',
            'override_subpanel_list_view' => 'subpanel-for-contacts',
            'context' => [
                'link' => 'direct_reports',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_DOCUMENTS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'documents',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_QUOTES_SHIP_TO',
            'context' => [
                'link' => 'quotes',
                'ignore_role' => 0,
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_QUOTES_BILL_TO',
            'context' => [
                'link' => 'billing_quotes',
                'ignore_role' => 0,
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_CONTRACTS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'contracts',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_PRODUCTS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'products',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_CAMPAIGN_LIST_SUBPANEL_TITLE',
            'context' => [
                'link' => 'campaigns',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_PROJECTS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'project',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_DATAPRIVACY_SUBPANEL_TITLE',
            'context' => [
                'link' => 'dataprivacy',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_PURCHASES_SUBPANEL_TITLE',
            'context' => [
                'link' => 'purchases',
            ],
        ],
    ],
];
