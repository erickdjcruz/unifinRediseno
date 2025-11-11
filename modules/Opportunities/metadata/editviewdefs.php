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

$viewdefs['Opportunities']['EditView'] = [
    'templateMeta' => ['maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
        'javascript' => '{$PROBABILITY_SCRIPT}',
    ],
    'panels' => [
        'default' => [

            [
                ['name' => 'name'],
                'account_name',
            ],
            [
                'opportunity_type',
                'lead_source',
            ],
            [
                'campaign_name',
                'next_step',
            ],
            [
                'description',
            ],
        ],

        'LBL_PANEL_ASSIGNMENT' => [
            [
                'assigned_user_name',

                ['name' => 'team_name'],
            ],
        ],
    ],
];
