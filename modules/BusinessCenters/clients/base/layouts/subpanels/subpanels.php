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
$viewdefs['BusinessCenters']['base']['layout']['subpanels'] = [
    'components' => [
        [
            'layout' => 'subpanel',
            'label' => 'LBL_ACCOUNTS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'business_center_accounts',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_CASES_SUBPANEL_TITLE',
            'context' => [
                'link' => 'business_center_cases',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_CONTACTS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'business_center_contacts',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_LEADS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'business_center_leads',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_USERS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'business_center_users',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_HOLIDAYS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'business_holidays',
            ],
        ],
    ],
];
