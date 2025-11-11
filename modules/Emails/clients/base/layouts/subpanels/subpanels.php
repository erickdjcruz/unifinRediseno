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
$viewdefs['Emails']['base']['layout']['subpanels'] = [
    'components' => [
        [
            'layout' => 'subpanel',
            'label' => 'LBL_ACCOUNTS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'accounts',
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
            'label' => 'LBL_CONTACTS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'contacts',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_OPPORTUNITY_SUBPANEL_TITLE',
            'context' => [
                'link' => 'opportunities',
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
            'label' => 'LBL_NOTES_SUBPANEL_TITLE',
            'context' => [
                'link' => 'notes',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_TASKS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'tasks',
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
            'label' => 'LBL_QUOTES_SUBPANEL_TITLE',
            'context' => [
                'link' => 'quotes',
            ],
        ],
    ],
];
