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

$viewdefs['ExternalUsers']['base']['layout']['subpanels'] = [
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
            'label' => 'LBL_OPPORTUNITIES_SUBPANEL_TITLE',
            'context' => [
                'link' => 'opportunities',
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
