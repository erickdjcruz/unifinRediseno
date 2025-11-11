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
$viewdefs['PurchasedLineItems']['base']['layout']['subpanels'] = [
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
            'label' => 'LBL_TASKS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'tasks',
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
            'label' => 'LBL_EMAILS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'emails',
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
            'label' => 'LBL_QLI_ADDONS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'addon_qlis',
            ],
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_RLI_ADDONS_SUBPANEL_TITLE',
            'context' => [
                'link' => 'addon_rlis',
            ],
        ],
    ],
];
